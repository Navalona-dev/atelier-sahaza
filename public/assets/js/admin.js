$(document).ready(function() {
    $('#loginPassword-toggle').click(function() {
      togglePasswordVisibility($('#login_form_password'));
    });
  });
  
  function togglePasswordVisibility(passwordField) {
    var fieldType = passwordField.attr('type');
    
    if (fieldType === 'password') {
        passwordField.attr('type', 'text');
        $('.btn-eye i').removeClass('bi-eye-slash').addClass('bi-eye');
    } else {
        passwordField.attr('type', 'password');
        $('.btn-eye i').removeClass('bi-eye').addClass('bi-eye-slash');
    }
  }

  $(document).ready(function () {
    $('#navbarDropdownHeader').click(function () {
        $(this).next('.dropdown-menu').toggle();
      });
  });