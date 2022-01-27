function submitAppsumoCaptureForm(emailAddress) {
  var xhr = new XMLHttpRequest();
  xhr.open('POST', 'https://hooks.zapier.com/hooks/catch/2130556/ot6a0xg/');
  xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
  xhr.send(encodeURI('email=' + emailAddress));
  alert("You're signed up!");
}
