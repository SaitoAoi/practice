function mail_check() {
  var flag_1 = 0;
  var mail = document.getElementById("user_id");
  var come1 = document.getElementById("come1");

  if (
    mail.value.match(
      /^([a-zA-Z0-9])+([a-zA-Z0-9\._-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9\._-]+)+$/
    )
  ) {
    flag_1 = 1;
    come1.innerHTML = "";
  } else if ((mail.value = "")) {
    flag_1 = 0;
    come1.innerHTML = "ユーザーIDを入力してください";
  } else {
    flag_1 = 0;
    come1.innerHTML = "正しいメールアドレスを入力してください";
  }

  return flag_1;
}

function user_name_check() {
  var user_name = document.getElementById("user_name");
  var come2 = document.getElementById("come2");
  var flag_2 = 0;

  if (user_name.value == "") {
    flag_2 = 0;
    come2.innerHTML = "ユーザー名を入力してください";
  } else if (user_name.value < 8) {
    flag_2 = 0;
    come2.innerHTML = "8文字以上で入力してください";
  } else {
    flag_2 = 1;
    come2.innerHTML = "";
  }

  return flag_2;
}

function pass_check() {
  var pass = document.getElementById("pass");
  var come3 = document.getElementById("come3");

  var flag_3 = 0;
  if (pass.value == "") {
    flag_3 = 0;
    come3.innerHTML = "パスワードを入力してください";
  } else if (pass.value.match(/^[A-Za-z0-9]+$/)) {
    if (pass.value < 8) {
      flag_3 = 0;
      come3.innerHTML = "8文字以上で入力してください";
    } else {
      flag_3 = 1;
      come3.innerHTML = "";
    }
  } else {
    flag_3 = 0;
    come3.innerHTML = "半角英数字で入力してください";
  }
  return flag_3;
}

function check() {
  var button = document.getElementById("button");
  var flag1 = mail_check();
  var flag2 = user_name_check();
  var flag3 = pass_check();

  var flag = flag1 + flag2 + flag3;

  if (flag == 3) {
    button.disabled = false;
  } else {
    button.disabled = true;
  }
}
