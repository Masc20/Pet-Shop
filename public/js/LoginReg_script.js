const container = document.getElementById(`container`);
const registerBtn = document.getElementById(`register`);
const loginBtn = document.getElementById(`login`);

const submitBtnReg = document.getElementById(`submitRegister`);
const submitBtnLogin = document.getElementById(`submitLogin`);

const showPasswordLogin = document.querySelector(`#show-password-login`);
const passwordFieldLogin = document.querySelector(`#pswd_login`);

const showPasswordRegister = document.querySelector(`#show-password-register`);
const passwordFieldRegister = document.querySelector(`#pswd_register`);

const showConfirmPasswordRegister = document.querySelector('#show-confirm-password-register');
const confirmPasswordFieldRegister = document.querySelector(`#pswd_register`);

const fields = {
  login: [
    { id: "usn_login", element: document.getElementById("usn_login") },
    { id: "pswd_login", element: document.getElementById("pswd_login") },
  ],
  register: [
    { id: "usn_register", element: document.getElementById("usn_register") },
    { id: "pswd_register", element: document.getElementById("pswd_register") },
    {
      id: "email_register",
      element: document.getElementById("email_register"),
    },
    {
      id: "phonenumber_register",
      element: document.getElementById("phonenumber_register"),
    },
    { id: "firstname", element: document.getElementById("firstname") },
    { id: "lastname", element: document.getElementById("lastname") },
    { id: "cnfrm_pswdr", element: document.getElementById("cnfrm_pswdr") },
  ],
};

let popupPopped = false;

registerBtn.addEventListener(`click`, (e)=>{
  if(popupPopped){
    e.preventDefault();
    return false;
  }
  container.classList.add("active");
  fields.login.forEach(fields => {
    fields.element.value = ``;
  });
});

loginBtn.addEventListener(`click`, (e)=>{
  if(popupPopped){
    e.preventDefault();
    return false;
  }
  container.classList.remove("active");
  fields.register.forEach(fields => {
    fields.element.value = ``;
  });
});

const popUpMessages = {
  invalidEmail: `
    <div class="popup-container">
      <div class="popup-header">
        <h2>Invalid Details</h2>
      </div>
      <div class="popup-body">
        <p><strong>A valid Email Address is required</strong>.</p>
      </div>
      <div class="popup-footer">
        <button id="close-popup">Close</button>
      </div>
    </div>
  `,
  EmptyFields: `
    <div class="popup-container">
      <div class="popup-header">
        <h3>Please fill in all fields</h3>
      </div>
      <div class="popup-body">
        <p><strong>All fields are required!</strong>.</p>
      </div>
      <div class="popup-footer">
        <button id="close-popup">Close</button>
      </div>
    </div>
  `,
  ConfirmPasswordNotTheSame: `
    <div class="popup-container">
      <div class="popup-header">
        <h3>Please fill in all fields</h3>
      </div>
      <div class="popup-body">
        <p><strong>Password are not the same!</strong>.</p>
      </div>
      <div class="popup-footer">
        <button id="close-popup">Close</button>
      </div>
    </div>
  `,
  SignedInSuccessfully: `
    <div class="popup-container">
      <div class="popup-header">
        <h3>Hello</h3>
      </div>
      <div class="popup-body-signed">
        <p><strong>Signed in Successfully!</strong>.</p>
      </div>
      <div class="popup-footer">
        <button id="close-popup">Close</button>
      </div>
    </div>
  `,
  SignedUpSuccessfully: `
    <div class="popup-container">
      <div class="popup-header">
        <h3>Hello </h3>
      </div>
      <div class="popup-body-signed">
        <p><strong>Signed up Successfully!</strong>.</p>
      </div>
      <div class="popup-footer">
        <button id="close-popup">Close</button>
      </div>
    </div>
  `,
};

const PopUpDiv = (popup_message) => {
  const popup = document.createElement('div');
  popup.innerHTML = popup_message;
  document.body.appendChild(popup);

  const closePopupButton = document.getElementById('close-popup');
  closePopupButton.addEventListener('click', () => {
    document.body.removeChild(popup);
    popupPopped = false;
  });
};

const validateComparePasswords = () => {
  let password;
  let confirmPassword;
  fields.forEach((fields) => {
    if (fields.type === 'password') {
      password = document.getElementById(fields.id[1]).value;
      confirmPassword = document.getElementById(fields.id[5]).value;
    }
  });
  return password == confirmPassword;
}

const validateFields = (fields) => {
  let allFieldsValid = true;

  fields.forEach((fields1) => {
    if (fields1.element.value.trim() === '') {
      allFieldsValid = false;
    }
  });
  return allFieldsValid;
};

const validEmail = () =>{
  const email = fields.register[2].element;
  if (email.matches(`:valid`)) {
    return true;
  }
  return false;
};

/* 
  window.location.href = 'https://www.example.com';
  window.location.replace('https://www.example.com');
  window.location.assign('https://www.example.com');
  window.location.reload(true);
  window.location.reload(false);

  const anchor = document.createElement('a');
  anchor.href = 'https://www.example.com';
  anchor.click();

  document.location.href = 'https://www.example.com';

  setTimeout(function() {
    window.location.href = 'https://www.example.com';
  }, 2000); // redirect after 2 seconds

*/

submitBtnLogin.addEventListener('click', (e) => {
  if (popupPopped) return;
  if (!validateFields(fields.login)) {
    PopUpDiv(popUpMessages.EmptyFields);
    e.preventDefault();
    popupPopped = true;
  } else {
    PopUpDiv(popUpMessages.SignedInSuccessfully)
    fields.login.forEach(fields => {
      fields.element.value = ``;
    });
  }
 
  // Submit the form or perform the desired action
});

submitBtnReg.addEventListener('click', (e) => { 
  if (popupPopped) return;
  if (!validateFields(fields.register)) {
    PopUpDiv(popUpMessages.EmptyFields);
    e.preventDefault();
    popupPopped = true;
  } else if (!validEmail()){
    PopUpDiv(popUpMessages.invalidEmail);
    e.preventDefault();
    popupPopped = true;
  } else if (!validateComparePasswords){
    PopUpDiv(popUpMessages.ConfirmPasswordNotTheSame);
    e.preventDefault();
    popupPopped = true;
  } else {
    PopUpDiv(popUpMessages.SignedUpSuccessfully)
    fields.register.forEach(fields => {
      fields.element.value = ``;
    });
  }
  // Submit the form or perform the desired action
});

const phonenumber_register = fields.register[3].element;
phonenumber_register.addEventListener(`input`, (e) =>{
    let value = e.target.value;
    const regex = /^[0-9]*$/;
    if (!regex.test(value)) {
        e.target.value = value.replace(/[^0-9]/g, '');
    }
});

showPasswordLogin.addEventListener(`click`, function () {
  this.classList.toggle(`fa-eye`);
  const Login_type = passwordFieldLogin.getAttribute(`type`) === "password" ? "text" : "password";
  passwordFieldLogin.setAttribute(`type`, Login_type);
});


showPasswordRegister.addEventListener(`click`, function () {
  this.classList.toggle(`fa-eye`);
  const Register_type = passwordFieldRegister.getAttribute(`type`) === "password" ? "text" : "password";
  passwordFieldRegister.setAttribute(`type`, Register_type);
});


showConfirmPasswordRegister.addEventListener(`click`, function () {
  this.classList.toggle(`fa-eye`);
  const Register_type = confirmPasswordFieldRegister.getAttribute(`type`) === "password" ? "text" : "password";
  confirmPasswordFieldRegister.setAttribute(`type`, Register_type);
});



