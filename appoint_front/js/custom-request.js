var url = "http://localhost:8000/";

function getRequestForm(url) {
        return fetch(url, {            
            method: 'GET',            
            headers: new Headers({
              'Accept': 'application/json',
              'Content-Type': 'application/json'
              //'Authorization' : 'Bearer '+ token
            }),
          })
          .then(response => response.json())                           
}


function postRequestForm(url, data) {
    
    var formData = new FormData();
    Object.keys(data).forEach(item => formData.append(item,data[item]))  
        
    return fetch(url, {
        method: 'POST',
        dataType: 'formData',
        body:  formData,
        headers: new Headers({
              'Accept': 'application/json',
              //'Authorization' : 'Bearer '+ token
            }),
      })
      .then(response => response.json())
}
