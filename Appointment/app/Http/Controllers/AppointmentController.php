<?php

namespace App\Http\Controllers;

use App\Http\Models\Appointment;
use App\Http\Models\Webinars;
use Illuminate\Http\Request;
use App\Mail\AppointmentCallRecived;
use Validator;
use Illuminate\Support\Facades\Mail;


class AppointmentController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $appoint = Appointment::with('webinar')->orderBy('date_begin', 'desc')->get();
        return $this->sendResponse($appoint->toArray(), 'Appointment exit');
    }

   
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id_webinars' => 'required|integer',            
            'date_begin' => 'nullable|date|date_format:Y-m-d',
            'emails' => 'nullable|string',
            'time' => 'nullable|date_format:H:i',
            'timezone' => 'nullable|string',
            'reason' => 'nullable|string',
            'organizer' => 'nullable|string'
        ]);
        if($validator->fails()){
            return $this->sendError('Error de validación.', $validator->errors());       
        }

        $webinar = Webinars::find($request->input('id_webinars'));
        if (is_null($webinar)) {
            return $this->sendError('Webinar no exist');
        }

        $appoint = new Appointment();
        $appoint->id_webinars = $request->input('id_webinars');
        $appoint->date_begin = $request->input('date_begin');
        $appoint->emails = $request->input('emails');
        $appoint->time = $request->input('time');
        $appoint->timezone = $request->input('timezone');
        $appoint->reason = $request->input('reason');
        $appoint->organizer = $request->input('organizer');
        

        $arr = explode(",", $request->input('emails'));
        $array_emails = array();        

        for ($i=0; $i < sizeof($arr); $i++) { 
            if (filter_var(trim($arr[$i]), FILTER_VALIDATE_EMAIL)) {
                array_push($array_emails, trim($arr[$i]));
            }else{
                return $this->sendError('There are invalid emails '.$arr[$i]);
            }
        }        
        $appoint->save();

        $date_start_sp = explode("-", $appoint->date_begin);
        $time_sp = explode(":", $appoint->time);
        $organizer       = 'World Wide Tech Connections';
        $organizer_email = $appoint->organizer;
        
        $cr              = "\n";
        $message    = 'BEGIN:VCALENDAR' . $cr;
        $message    .= 'PRODID:-//i3SoftWebsite//cal_events/NONSGML v1.0//EN' . $cr;
        $message    .= 'VERSION:2.0' . $cr;
        $message    .= 'CALSCALE:GREGORIAN' . $cr;
        $message    .= 'METHOD:REQUEST' . $cr;
        $message    .= 'BEGIN:VEVENT' . $cr;
        $message    .= 'UID:' . md5(uniqid(mt_rand(), true)) . '@test.com' . $cr;
        $message    .= 'CREATED:' . gmdate('Ymd').'T'. gmdate('His') . 'Z' . $cr;
        $message    .= 'DTSTAMP:' . gmdate('Ymd') . 'T' . gmdate('His') . 'Z' . $cr;
        $message    .= 'DTSTART;TZID=' . $appoint->timezone.':'. $date_start_sp[0].$date_start_sp[1].$date_start_sp[2].'T'.$time_sp[0].$time_sp[1] .'00Z'. $cr;
        $message    .= 'DTEND;TZID=' . $appoint->timezone.':'.$date_start_sp[0].$date_start_sp[1].$date_start_sp[2].'T'.$time_sp[0].$time_sp[1] .'00Z'. $cr;
        $message    .= "SUMMARY:". html_entity_decode($appoint->reason, ENT_QUOTES, 'UTF-8').$cr;          
        $message    .= "ORGANIZER;CN={$organizer}:mailto:{$organizer_email}{$cr}";
        $message    .= "SEQUENCE:0{$cr}";
        $message    .= 'DESCRIPTION:' . html_entity_decode($appoint->webinar->description, ENT_QUOTES, 'UTF-8') . $cr;
        for ($i=0; $i < sizeof($array_emails); $i++) { 
          $message    .= 'ATTENDEE;CUTYPE=INDIVIDUAL;ROLE=REQ-PARTICIPANT;PARTSTAT=NEEDS-ACTION;RSVP=TRUE;X-NUM-GUESTS=0:MAILTO:'.$array_emails[$i].$cr;
        }

        $message .= 'END:VEVENT' . $cr;
        $message .= 'END:VCALENDAR' . $cr;

        $file = fopen("invite.ics","w");
        fwrite($file,$message);
        fclose($file);

        try{
            $subject = $appoint->reason;
            Mail::to($array_emails)->send(new AppointmentCallRecived($appoint), function ($messag) use($subject) {                
                    $messag->getHeaders()->addTextHeader('Content-Type', 'text/calendar')
                    ->addTextHeader('method', 'REQUEST')
                    ->addTextHeader('charset', 'UTF-8')
                    ->addTextHeader('Content-Transfer-Encoding', '7bit');
                    $messag->subject($subject);
            });
            return $this->sendResponse($appoint->toArray(), 'Appointment exit');
           
        }catch(\Exception $e){
            Appointment::find($appoint->id)->delete();
            return $this->sendError('Error send email '.$e->getMessage());
        }
        
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Appointment  $appointment
     * @return \Illuminate\Http\Response
     */
    public function show($id_appoint)
    {
        $appoint = Appointment::with('webinar')->find($id_appoint);
        if (is_null($appoint)) {
            return $this->sendError('Appoint no exist');
        }
        
        $arr = explode(',', $appoint['emails']);
        $aux = array();
        for ($i=0; $i < sizeof($arr); $i++) { 
             array_push($aux, $arr[$i]);  
        }

        $out = compact('appoint', 'aux');
        
        return $this->sendResponse($out, 'Appointment exit');
    }

   
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Appointment  $appointment
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Appointment $appointment)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Appointment  $appointment
     * @return \Illuminate\Http\Response
     */
    public function destroy(Appointment $appointment)
    {
        //
    }
}
