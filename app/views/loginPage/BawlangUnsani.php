<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="LoginReg_style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
</head>
<body>
    <div class="container" id="container">
        <div class="form-container sign-up">
            <form>
                <div>
                    <h1>Create a account</h1>
                </div>
                <div class="fields">
                    <input id="usn_register" type="text" name="Username" placeholder="Username">
                    <input id="email_register" type="text" name="Email" placeholder="Email (example@email.com)"
                        pattern="[a-zA-Z0-9]+[@]+[a-zA-Z0-9]+[.]+[a-zA-Z0-9]+$">
                    <input id="firstname" type="text" name="firstname" placeholder="Firstname ">
                    <input id="lastname" type="text" name="lastname" placeholder="Lastname ">
                    <input id="phonenumber_register" type="text" name="Phone Number" placeholder="Phone Number (09*********)" pattern="[0-9]*$">
                </div>
                
                <div class="fields password-container">
                    <input id="pswd_register" type="password" name="password" placeholder="Password">
                    <i class="fa-solid fa-eye-slash" id="show-password-register"></i>
                </div>
                <div class="fields password-container">
                    <input id="cnfrm_pswdr" type="password" name="password" placeholder="Confirm Password">
                    <i class="fa-solid fa-eye-slash" id="show-confirm-password-register"></i>
                </div>
               
                <button id="submitRegister" type="button">Sign up</button>
            </form>
        </div>
        <div class="form-container sign-in">
            <form>
                <div>
                    <h1>Sign-in</h1>
                </div>
                <div class="fields">
                    <input id="usn_login" type="text" name="Username" placeholder="Username">
                </div>
                <div class="password-container fields">
                    <input id="pswd_login" type="password" name="password" placeholder="Password">
                    <i class="fa-solid fa-eye-slash" id="show-password-login"></i>
                </div>
                <a href="#">Forgot Password?</a>
                <button id="submitLogin" type="button">Sign In</button>
            </form>
        </div>
        <div class="toggle-container">
            <div class="toggle">
                <div class="toggle-panel toggle-left">
                    <h1>Welcome Back!</h1>
                    <p>Enter your personal informations to use this site</p>
                    <button class="hidden" id="login" type="button">Sign In</button>
                </div>
                <div class="toggle-panel toggle-right">
                    <h1>Hi, Kid!</h1>
                    <p>Register with your personal informations to use this site</p>
                    <button class="hidden" id="register" type="button">Sign Up</button>
                </div>
            </div>
        </div>
    </div>
    <script src="LoginReg_script.js"></script>
</body>
</html>