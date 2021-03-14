//WARNING: if you make changes to the master version of this file, you should also rename the 
//version number of the file to chat-inner-x.y.z+1 (and use git mv) to prevent user's browsers caches from referring to
//an old version. 
//Then make a change into the server server config file entry 'chatInnerJSFilename', and the top of search-secure script,
// with the correct default version number.
//
//Language & messages configuration
//Note: also see /config/messages.json for further messages configuration
var lsmsg = {
    "defaultLanguage" : "en",
    "msgs": {
        "en":{
              "defaultYourName": "Your Name",
              "defaultYourEmail" : "Your Email",
              "loggedIn": "Logged in. Please wait..",
              "passwordWrong": "Sorry, your password is not correct.",
              "forumPasswordWrong": "Sorry, your forum password is not correct.",
              "passwordStored": "Thanks, your password is now set.",
              "registration": "Thanks for registering.  To confirm your email address we've sent an email with a link in it, which you should click within a day.",
              "badResponse": "Sorry, response is: ",
              "more": "More",
              "lostConnection": "Warning: Waiting for a good connection.",
              "blankMessage": "Warning: you tried to send a blank message.",
              "messageQueued": "Warning: your message 'MESSAGE' will be sent when a connection is re-established.",
              "subscribed": "You have successfully subscribed.",
              "subscriptionDenied": "Sorry, you are not authorised to subscribe to this forum."           
        },
        "es":{
              "defaultYourName": "Tu Nombre",
              "defaultYourEmail": "Su e-mail",
              "loggedIn": "Conectado Por favor, espere..",
              "passwordWrong": "Lo siento, la contraseña no es correcta.",
              "forumPasswordWrong": "Lo sentimos, la contraseña de tu foro no es correcta.",
              "passwordStored": "Gracias, su contraseña se establece ahora.",
              "registration": "Gracias por registrarse. Para confirmar su dirección de correo electrónico que've enviado un correo electrónico con un enlace en ella, lo que debe hacer clic en un día.",
              "badResponse": "Lo siento, la respuesta es: ",
              "more": "Mas",
              "lostConnection": "Advertencia: Esperando una buena conexión.",
              "blankMessage": "Advertencia: ha intentado enviar un mensaje en blanco.",
              "messageQueued": "Advertencia: su mensaje 'MESSAGE' será enviado cuando se restablezca una conexión.",
              "subscribed": "Te has suscripto satisfactoriamente.",
              "subscriptionDenied": "Lo sentimos, no estás autorizado para suscribirte a este foro."
        }, 
        "pt": {
              "defaultYourName": "Seu Nome",
              "defaultYourEmail": "Seu Email",
              "loggedIn": "Iniciado. Aguarde..",
              "passwordWrong": "Desculpe, sua senha não está correta.",
              "forumPasswordWrong": "Desculpe, sua senha do fórum não está correta.",
              "passwordStored": "Obrigado, sua senha está agora definida.",
              "registration": "Obrigado por se registrar. Para confirmar seu endereço de e-mail, enviamos um e-mail com um link, que você deve clicar dentro de um dia.",
              "badResponse": "Desculpe, a resposta é: ",
              "more": "Mais",
              "lostConnection": "Aviso: Esperando uma boa conexão.",
              "blankMessage": "Aviso: você tentou enviar uma mensagem em branco.",
              "messageQueued": "Aviso: sua mensagem 'MESSAGE' será enviada quando uma conexão for restabelecida.",
              "subscribed": "Você se inscreveu com sucesso.",
              "subscriptionDenied": "Desculpe, você não está autorizado a assinar este fórum."
        },
         "ch": {
        	 "defaultYourName": "您的名字",
              "defaultYourEmail": "您的电子邮件",
              "loggedIn": "已登录。请稍候..",
              "passwordWrong": "对不起，您的密码不正确。",
              "forumPasswordWrong": "对不起，您的论坛密码不正确。",
              "passwordStored": "谢谢，您的密码已设置。",
              "registration": "感谢您的注册。为确认您的电子邮件地址，我们已发送了一封带有链接的电子邮件，请在一天之内单击该链接。",
              "badResponse": "对不起，响应为：",
              "more": "更多",
              "lostConnection": "警告：等待良好的连接。",
              "blankMessage": "警告：您试图发送空白消息。",
              "messageQueued": "警告：重新建立连接后，您的消息'MESSAGE'将被发送。",
              "subscribed": "您已成功订阅。",
              "subscriptionDenied": "很抱歉，您无权订阅此论坛。"
        },
         "de": {
              "defaultYourName": "Ihr Name",
              "defaultYourEmail": "Ihre E-Mail",
              "loggedIn": "Eingeloggt. Bitte warten ..",
              "passwordWrong": "Ihr Passwort ist leider nicht korrekt.",
              "forumPasswordWrong": "Entschuldigung, Ihr Forum-Passwort ist nicht korrekt.",
              "passwordStored": "Danke, Ihr Passwort ist jetzt festgelegt.",
              "registration": "Vielen Dank für Ihre Registrierung. Um Ihre E-Mail-Adresse zu bestätigen, haben wir eine E-Mail mit einem Link gesendet, auf den Sie innerhalb eines Tages klicken sollten.",
              "badResponse": "Entschuldigung, die Antwort lautet:",
              "more": "mehr",
              "lostConnection": "Warnung: Warten auf eine gute Verbindung.",
              "blankMessage": "Warnung: Sie haben versucht, eine leere Nachricht zu senden.",
              "messageQueued": "Warnung: Ihre Nachricht 'MESSAGE' wird gesendet, wenn eine Verbindung wiederhergestellt wird.",
              "subscribed": "Sie haben erfolgreich abonniert.",
              "subscriptionDenied": "Sie sind leider nicht berechtigt, dieses Forum zu abonnieren."
        },
        "fr": {
              "defaultYourName": "Votre nom",
              "defaultYourEmail": "Votre e-mail",
              "loggedIn": "Connecté. Veuillez patienter..",
              "passwordWrong": "Désolé, votre mot de passe n'est pas correct.",
              "forumPasswordWrong": "Désolé, le mot de passe de votre forum n'est pas correct.",
              "passwordStored": "Merci, votre mot de passe est maintenant défini.",
              "registration": "Merci de vous être inscrit. Pour confirmer votre adresse e-mail, nous vous avons envoyé un e-mail contenant un lien, sur lequel vous devez cliquer dans la journée.",
              "badResponse": "Désolé, la réponse est:",
              "more": "plus",
              "lostConnection": "Avertissement: En attente d'une bonne connexion.",
              "blankMessage": "Attention: vous avez essayé d'envoyer un message vide.",
              "messageQueued": "Attention: votre message 'MESSAGE' sera envoyé lorsqu'une connexion sera rétablie.",
              "subscribed": "Vous êtes abonné avec succès.",
              "subscriptionDenied": "Désolé, vous n'êtes pas autorisé à vous abonner à ce forum."
        },
        "hi": {
              "defaultYourName": "आपका नाम",
              "defaultYourEmail": "आपका ईमेल",
              "loggedIn": "लॉग इन करें। कृपया प्रतीक्षा करें..",
              "passwordWrong": "क्षमा करें, आपका पासवर्ड सही नहीं है।",
              "forumPasswordWrong": "क्षमा करें, आपका फ़ोरम पासवर्ड सही नहीं है।",
              "passwordStored": "धन्यवाद, आपका पासवर्ड अब सेट है।",
              "registration": "पंजीकरण के लिए धन्यवाद। आपके ईमेल पते की पुष्टि करने के लिए हमने इसमें एक लिंक के साथ एक ईमेल भेजा है, जिसे आपको एक दिन के भीतर भेजना चाहिए।",
              "badResponse": "क्षमा करें, प्रतिक्रिया है:",
              "more" : "ज्यादा",
              "lostConnection": "चेतावनी: एक अच्छे संबंध की प्रतीक्षा कर रहा है।",
              "blankMessage": "चेतावनी: आपने एक रिक्त संदेश भेजने का प्रयास किया है।",
              "messageQueued": "चेतावनी: आपका संदेश 'MESSAGE' तब भेजा जाएगा जब कोई कनेक्शन फिर से स्थापित हो, सेट करें।",
              "subscribed": "आपने सफलतापूर्वक सदस्यता ले ली है।",
              "subscriptionDenied": "क्षमा करें, आप इस मंच की सदस्यता के लिए अधिकृत नहीं हैं।"
        },
        "ru": {
              "defaultYourName": "Ваше имя",
              "defaultYourEmail": "Ваш адрес электронной почты",
              "loggedIn": "Выполнен вход. Подождите..",
              "passwordWrong": "Извините, ваш пароль неверен.",
              "forumPasswordWrong": "К сожалению, ваш пароль на форуме неверен.",
              "passwordStored": "Спасибо, ваш пароль установлен.",
              "registration": "Спасибо за регистрацию. Чтобы подтвердить ваш адрес электронной почты, мы отправили электронное письмо со ссылкой, которую вы должны нажать в течение дня.",
              "badResponse": "Извините, ответ:",
              "more": "больше",
              "lostConnection": "Предупреждение: ожидается хорошее соединение.",
              "blankMessage": "Предупреждение: вы пытались отправить пустое сообщение.",
              "messageQueued": "Предупреждение: ваше сообщение 'MESSAGE' будет отправлено при восстановлении соединения.",
              "subscribed": "Вы успешно подписались.",
              "subscriptionDenied": "Извините, у вас нет прав для подписки на этот форум"
        },
        "jp":{
              "defaultYourName": "あなたの名前",
              "defaultYourEmail": "あなたのメール",
              "loggedIn": "ログインしました。お待ちください...",
              "passwordWrong": "申し訳ありませんが、パスワードが正しくありません。",
              "forumPasswordWrong": "申し訳ありませんが、フォーラムのパスワードが正しくありません。",
              "passwordStored": "ありがとうございます。パスワードが設定されました。",
              "registration": "登録いただきありがとうございます。メールアドレスを確認するために、リンクを記載したメールを送信しました。1日以内にクリックしてください。",
              "badResponse": "申し訳ありませんが、応答は:",
              "more": "もっともっと",
              "lostConnection": "警告:良好な接続を待っています。",
              "blankMessage": "警告:空のメッセージを送信しようとしました。",
              "messageQueued": "警告:接続が再確立されると、メッセージ'MESSAGE'が送信されます。",
              "subscribed": "購読に成功しました。",
              "subscriptionDenied": "申し訳ありませんが、このフォーラムにサブスクライブする権限がありません。"
        },
        "bg": {
              "defaultYourName": "আপনার নাম",
              "defaultYourEmail": "আপনার ইমেল",
              "loggedIn": "লগ ইন হয়েছে Please দয়া করে অপেক্ষা করুন ..",
              "passwordWrong": "দুঃখিত, আপনার পাসওয়ার্ডটি সঠিক নয়।",
              "forumPasswordWrong": "দুঃখিত, আপনার ফোরামের পাসওয়ার্ড সঠিক নয়",
              "passwordStored": "ধন্যবাদ, আপনার পাসওয়ার্ড এখন সেট করা আছে।",
              "registration": "নিবন্ধের জন্য ধন্যবাদ। আপনার ইমেল ঠিকানাটি নিশ্চিত করতে আমরা এতে একটি লিঙ্ক সহ একটি ইমেল প্রেরণ করেছি, যা আপনার একদিনের মধ্যে ক্লিক করা উচিত",
              "badResponse": "দুঃখিত, প্রতিক্রিয়াটি হ'ল:",
              "more": "বেশি",
              "lostConnection": "সতর্কতা: ভাল সংযোগের জন্য অপেক্ষা করা হচ্ছে।",
              "blankMessage": "সতর্কতা: আপনি একটি ফাঁকা বার্তা প্রেরণের চেষ্টা করেছেন।",
              "messageQueued": "সতর্কতা: কোনও সংযোগ পুনরায় প্রতিষ্ঠিত হওয়ার পরে আপনার বার্তা 'MESSAGE' প্রেরণ করা হবে।",
              "subscribed": "আপনি সফলভাবে সাবস্ক্রাইব করেছেন।",
              "subscriptionDenied": "দুঃখিত, আপনি এই ফোরামে সাবস্ক্রাইব করার অনুমতিপ্রাপ্ত নন।"
        },
        "ko": {
              "defaultYourName": "사용자 이름",
              "defaultYourEmail": "이메일",
              "loggedIn": "로그인되었습니다. 잠시만 기다려주십시오..",
              "passwordWrong": "죄송합니다. 비밀번호가 올바르지 않습니다.",
              "forumPasswordWrong": "죄송합니다. 포럼 비밀번호가 올바르지 않습니다.",
              "passwordStored": "감사합니다. 이제 비밀번호가 설정되었습니다.",
              "registration": "등록 해 주셔서 감사합니다. 귀하의 이메일 주소를 확인하기 위해 링크가 포함 된 이메일을 보냈습니다. 링크를 하루 안에 클릭하셔야합니다.",
              "badResponse": "죄송합니다. 응답 :",
              "more": "더보기",
              "lostConnection": "경고 : 좋은 연결을 기다리는 중입니다.",
              "blankMessage": "경고 : 빈 메시지를 보내려고했습니다.",
              "messageQueued": "경고 : 연결이 다시 설정되면 'MESSAGE'메시지가 전송됩니다.",
              "subscribed": "구독했습니다.",
              "subscriptionDenied": "죄송합니다.이 포럼에 가입 할 권한이 없습니다."
        },
        "pu": {
              "defaultYourName": "ਤੁਹਾਡਾ ਨਾਮ",
              "defaultYourEmail": "ਤੁਹਾਡਾ ਈਮੇਲ",
              "loggedIn": "ਲੌਗ ਇਨ ਹੋਇਆ. ਉਡੀਕੋ ਜੀ..",
              "passwordWrong": "ਮੁਆਫ ਕਰਨਾ, ਤੁਹਾਡਾ ਪਾਸਵਰਡ ਸਹੀ ਨਹੀਂ ਹੈ।",
              "forumPasswordWrong": "ਮੁਆਫ ਕਰਨਾ, ਤੁਹਾਡਾ ਫੋਰਮ ਪਾਸਵਰਡ ਸਹੀ ਨਹੀ ਹੈ।",
              "passwordStored": "ਧੰਨਵਾਦ, ਤੁਹਾਡਾ ਪਾਸਵਰਡ ਹੁਣ ਸੈੱਟ ਹੋ ਗਿਆ ਹੈ।",
              "registration": "ਰਜਿਸਟਰ ਕਰਨ ਲਈ ਧੰਨਵਾਦ. ਤੁਹਾਡੇ ਈਮੇਲ ਪਤੇ ਦੀ ਪੁਸ਼ਟੀ ਕਰਨ ਲਈ ਅਸੀਂ ਇਸ ਵਿੱਚ ਇੱਕ ਲਿੰਕ ਦੇ ਨਾਲ ਇੱਕ ਈਮੇਲ ਭੇਜਿਆ ਹੈ, ਜਿਸ ਨੂੰ ਤੁਹਾਨੂੰ ਇੱਕ ਦਿਨ ਦੇ ਅੰਦਰ ਕਲਿੱਕ ਕਰਨਾ ਚਾਹੀਦਾ ਹੈ.",
              "badResponse": "ਮੁਆਫ ਕਰਨਾ, ਜਵਾਬ ਹੈ:",
              "more": "ਹੋਰ",
              "lostConnection": "ਚੇਤਾਵਨੀ: ਚੰਗੇ ਕੁਨੈਕਸ਼ਨ ਦੀ ਉਡੀਕ ਹੈ।",
              "blankMessage": "ਚੇਤਾਵਨੀ: ਤੁਸੀਂ ਇੱਕ ਖਾਲੀ ਸੁਨੇਹਾ ਭੇਜਣ ਦੀ ਕੋਸ਼ਿਸ਼ ਕੀਤੀ।",
              "messageQueued": "ਚੇਤਾਵਨੀ: ਜਦੋਂ ਤੁਹਾਡਾ ਕੁਨੈਕਸ਼ਨ ਦੁਬਾਰਾ ਸਥਾਪਤ ਕੀਤਾ ਜਾਂਦਾ ਹੈ ਤਾਂ ਤੁਹਾਡਾ ਸੁਨੇਹਾ 'MESSAGE' ਭੇਜਿਆ ਜਾਏਗਾ।",
              "subscribed": "ਤੁਸੀਂ ਸਫਲਤਾਪੂਰਵਕ ਗਾਹਕੀ ਲੈ ਲਈ ਹੈ.",
              "subscriptionDenied": "ਮੁਆਫ ਕਰਨਾ, ਤੁਹਾਨੂੰ ਇਸ ਫੋਰਮ ਦਾ ਮੈਂਬਰ ਬਣਨ ਦਾ ਅਧਿਕਾਰ ਨਹੀਂ ਹੈ."
        },
        "it": {
              "defaultYourName": "Il tuo nome",
              "defaultYourEmail": "La tua email",
              "loggedIn": "Accesso effettuato. Attendi ..",
              "passwordWrong": "Spiacenti, la tua password non è corretta.",
              "forumPasswordWrong": "Siamo spiacenti, la password del forum non è corretta.",
              "passwordStored": "Grazie, la tua password è ora impostata.",
              "registration": "Grazie per esserti registrato. Per confermare il tuo indirizzo e-mail abbiamo inviato un'e-mail con un collegamento, su cui devi fare clic entro un giorno.",
              "badResponse": "Spiacenti, la risposta è:",
              "more": "Di più",
              "lostConnection": "Avviso: in attesa di una buona connessione.",
              "blankMessage": "Attenzione: hai tentato di inviare un messaggio vuoto.",
              "messageQueued": "Attenzione: il tuo messaggio 'MESSAGE' verrà inviato quando verrà ristabilita la connessione.",
              "subscribed": "Ti sei iscritto con successo.",
              "subscriptionDenied": "Spiacenti, non sei autorizzato a iscriverti a questo forum."
        },
        "in": {
              "defaultYourName": "Namamu",
              "defaultYourEmail": "Email Anda",
              "loggedIn": "Sudah masuk. Harap tunggu ..",
              "passwordWrong": "Maaf, password anda salah.",
              "forumPasswordWrong": "Maaf, kata sandi forum Anda salah.",
              "passwordStored": "Terima kasih, sandi Anda sekarang telah disetel.",
              "registration": "Terima kasih telah mendaftar. Untuk mengonfirmasi alamat email Anda, kami telah mengirimkan email dengan tautan di dalamnya, yang harus Anda klik dalam satu hari.",
              "badResponse": "Maaf, tanggapannya:",
              "more": "Lebih",
              "lostConnection": "Peringatan: Menunggu koneksi yang baik.",
              "blankMessage": "Peringatan: Anda mencoba mengirim pesan kosong.",
              "messageQueued": "Peringatan: pesan 'MESSAGE' Anda akan dikirim saat koneksi tersambung kembali.",
              "subscribed": "Anda berhasil berlangganan.",
              "subscriptionDenied": "Maaf, Anda tidak diizinkan untuk berlangganan forum ini."
        },
         "cht":{
              "defaultYourName":"您的名字",
              "defaultYourEmail":"您的電子郵件",
              "loggedIn":"已登錄。請稍候..",
              "passwordWrong":"對不起，您的密碼不正確。",
              "forumPasswordWrong":"對不起，您的論壇密碼不正確。",
              "passwordStored":"謝謝，您的密碼已設置。",
              "registration":"感謝您的註冊。為確認您的電子郵件地址，我們已發送了一封帶有鏈接的電子郵件，請在一天之內單擊該鏈接。",
              "badResponse":"對不起，響應為：",
              "more":"更多",
              "lostConnection":"警告：等待良好的連接。",
              "blankMessage":"警告：您試圖發送空白消息。",
              "messageQueued":"警告：重新建立連接後，您的消息'MESSAGE'將被發送。",
              "subscribed":"您已成功訂閱。",
              "subscriptionDenied":"很抱歉，您無權訂閱此論壇。"
        }    
    }
}
var lang = lsmsg.defaultLanguage;       





function myTrim(x)
{
	return x.replace(/^\s+|\s+$/gm,'');
}

function getCookie(cname)
{
	var name = cname + "=";
	var ca = document.cookie.split(';');
	for(var i=0; i<ca.length; i++)
	{
		var c = myTrim(decodeURIComponent(ca[i]));// ie8 didn't support .trim();
		if (c.indexOf(name)==0) return c.substring(name.length,c.length);
	}
	return "";
}


function cookieOffset()
{
  //Should output: Thu,31-Dec-2020 00:00:00 GMT
  var cdate = new Date;
  var expirydate=new Date();
  expirydate.setTime(expirydate.getTime()+(365*3*60*60*24*1000))
  var write = expirydate.toGMTString();
  
  return write;
}

function hideKeyboard(element) {
    element.attr('readonly', 'readonly'); // Force keyboard to hide on input field.
    element.attr('disabled', 'true'); // Force keyboard to hide on textarea field.
    setTimeout(function() {
        element.blur();  //actually close the keyboard
        // Remove readonly attribute after keyboard is hidden.
        element.removeAttr('readonly');
        element.removeAttr('disabled');
    }, 100);
}

function assignPortToURL(url, portNum) {
    if((portNum)&&(portNum != "")) {
        url = url.replace(/\/\/(.*?)\//, "//$1:" + portNum + "/");
    } else {
        //Do nothing if no port number 
           
    }
    return url;
}

var commentLayer="comments";
var ssshoutFreq = 5000;
var ssshoutHasFocus = true;
var myLoopTimeout;
var whisperOften = "1.1.1.1:2"; //defaults to admin user's ip and id
var whisperSite = "1.1.1.1:2"; //defaults to admin user's ip and id
var cs = 2438974;
var ssshoutServer = "https://atomjump.com/api";  //https://atomjump.com/api  normally
var typingTimer = 0;
var startShoutId = 0;		//start of a typing session

var currentlyTyping = false;
var records = 25;			//once more is clicked we will allow more
var showMore = 25;
//Moved to seach-secure.php: var sendPublic = false;  //if true, override to a public social network response
var shortCode = "";  //shortcode for social network eg. twt, fbk
var publicTo = "";  //who on social network we are sending to eg. twitter handle
var globResults = {};
var modifiedEmail = false;		//Switched to true after a user has started modifying their email address with a keyboard.


//Check for android browser
var navU = navigator.userAgent;

// Android Mobile
var isAndroid = navigator.userAgent.indexOf('Android') >= 0;
var webkitVer = parseInt((/WebKit\/([0-9]+)/.exec(navigator.appVersion) || 0)[1],10) || void 0; // also match AppleWebKit
var isNativeAndroid = isAndroid && webkitVer <= 534 && navigator.vendor.indexOf('Google') == 0;

if(isAndroid) {
		isNativeAndroid = true;
}


function initAtomJumpFeedback(params)
{
	commentLayer = params.uniqueFeedbackId;
	whisperOften = params.myMachineUser;
	whisperSite = params.myMachineUser;
	if(params.server){
	  ssshoutServer = params.server;
 }

}

//Run automatically
if(typeof ajFeedback !== 'undefined') {
	initAtomJumpFeedback(ajFeedback);
} 




function receiveMessage(msg)
{
	if(typeof jQuery == 'undefined') {
		//IE was complaining on the form close that jquery no longer existed in this frame.
	} else {
		if(!msg) {
			//Settings
			if($("#comment-popup-content").is(':visible')) {
				$("#comment-popup-content").hide();
				$("#comment-upload").hide();
				$("#comment-options").show();
				$("#comment-emojis").hide();
				var targetOrigin = getParentUrl();		//This is in search-secure
				parent.postMessage( {'highlight': "options" }, targetOrigin );
			} else {
				$("#comment-popup-content").show();
				$("#comment-upload").hide();
				$("#comment-options").hide();
				$("#comment-emojis").hide();
				var targetOrigin = getParentUrl();		//This is in search-secure
				parent.postMessage( {'highlight': "none" }, targetOrigin );
			}
		} else {
			switch(msg) {
			
				case 'toggle': 
					//Toggle options
					//Settings
					if($("#comment-popup-content").is(':visible')) {
						$("#comment-popup-content").hide();
						$("#comment-upload").hide();
						$("#comment-options").show();
						$("#comment-emojis").hide();
						var targetOrigin = getParentUrl();		//This is in search-secure
						parent.postMessage( {'highlight': "options" }, targetOrigin );
					} else {
						$("#comment-popup-content").show();
						$("#comment-upload").hide();
						$("#comment-options").hide();
						$("#comment-emojis").hide();
						var targetOrigin = getParentUrl();		//This is in search-secure
						parent.postMessage( {'highlight': "none" }, targetOrigin );
					}
					
				break;
				
				case 'upload':
					//Upload
					if($("#comment-popup-content").is(':visible')) {
						$("#comment-popup-content").hide(); 
						$("#comment-upload").show();
						$("#comment-options").hide();
						$("#comment-emojis").hide();
						var targetOrigin = getParentUrl();		//This is in search-secure
						parent.postMessage( {'highlight': "upload" }, targetOrigin );
					} else {
						$("#comment-popup-content").show();
						$("#comment-upload").hide();
						$("#comment-options").hide();
						$("#comment-emojis").hide();
						var targetOrigin = getParentUrl();		//This is in search-secure
						parent.postMessage( {'highlight': "none" }, targetOrigin );
					}
				break;
				
				case 'emojis':
					//Emojis
					if($("#comment-popup-content").is(':visible')) {
						$("#comment-popup-content").hide(); 
						$("#comment-emojis").show();
						$("#comment-options").hide();
						$("#comment-upload").hide();
						var targetOrigin = getParentUrl();		//This is in search-secure
						parent.postMessage( {'highlight': "emojis" }, targetOrigin );
					} else {
						$("#comment-popup-content").show();
						$("#comment-emojis").hide();
						$("#comment-options").hide();
						$("#comment-upload").hide();
						var targetOrigin = getParentUrl();		//This is in search-secure
						parent.postMessage( {'highlight': "none" }, targetOrigin );
					}
				break;
				
				case 'title':
					//Nothing to do here, but we have the title.
					
				break;
			
				default:
					//Do nothing
				break;
			}
		
		}
	}
	
}

var waitForCommitTimer = false;
function waitForCommitFinish()
{
	
	waitForCommitTimer = setTimeout(function(){
		if($('#typing').val() == 'on') {
			waitForCommitFinish();
		} else {
			clearTimeout(waitForCommitTimer);
			registerNewKeypress();
		}
		
	}, 50);
}

var msg = function() {
	this.localMsgId = 1;
	this.localMsg = {};	//Must be an object for iteration
	this.requests = {};
	this.currentRequestId = 1;
	
	/*
	
		msg.status:
		"requestId" :  started typing a new message, so waiting for an id back from the server
		"committed" :  message has been committed locally (and is on a queue), but not actually started to be sent
		"deactivate" :  message should be deactivated/hidden
		"restarting" :  typing is restarting, so the 'typing' message needs to be shown again
		"typing" :    user is currently typing
		"sending" :   message has been taken off the sending queue to the server
		"complete" :   message confirmation back from the server - has been sent to the server
		"gotid"   :  have received a reply from the server - and the server message id has been set.
		"lostid"   : have received a message from the server and didn't get an id, or we had an error from the server and so got no id either
	
	*/
	
	function newMsg(whisper)
	{
		this.localMsg[this.localMsgId] = {};
		this.localMsg[this.localMsgId].typing = "on";
		this.localMsg[this.localMsgId].shoutId = "";
		this.localMsg[this.localMsgId].status = "requestId";
		this.localMsg[this.localMsgId].whisper = whisper;
		this.localMsg[this.localMsgId].whisperOften = whisperOften;
		this.localMsg[this.localMsgId].shouted = $('#shouted').val();
		this.localMsg[this.localMsgId].shortCode = shortCode;
		this.localMsg[this.localMsgId].shortCode = publicTo;
	    this.processEachMsg();

		records = showMore;	//If we had clicked more before, we want to reduce again

		return false;
	}
	
	
	function commitMsg(whisper)
	{
		
		//Commiting a new message locally		
		if(this.localMsg[this.localMsgId]) {
				
			if(sendPublic == true) {
			   //override
			   whisper = false;
			} else {
			   whisper = true;
			}
			this.localMsg[this.localMsgId].whisper = whisper;
			this.localMsg[this.localMsgId].whisperOften = whisperOften;
			this.localMsg[this.localMsgId].shouted = $('#shouted').val();		//Save whatever was entered when pushing enter or clicking send
			this.localMsg[this.localMsgId].typing = "off";
			this.localMsg[this.localMsgId].status = "committed";
			this.localMsg[this.localMsgId].shortCode = shortCode;
			this.localMsg[this.localMsgId].publicTo = publicTo;

			//Clear the shout input box
			$('#shouted').val('');
			$('#shouted').removeAttr('value');	
			if(isNativeAndroid) {
				//If the keyboard is left on, the DOM isn't updated
				hideKeyboard($('#shouted'));
			}
			$('#shouted').focus();



			//Go ahead and start processing all messages outstanding
			this.processEachMsg();

			this.localMsgId ++;		//next message local id


			//Allow a new message to be generated by typing again
			currentlyTyping = false;

		
			records = showMore;	//If we had clicked more before, we want to reduce again
		} else {
			//Show warning for blank message sent
			$("#warnings").html(lsmsg.msgs[lang].blankMessage);
			$("#warnings").show();
			$("#shouted").val('');	//Clear off
		
		}
		return false;
	}
	

	function finishMsg(msgId)
	{
		//Remove from local array
		this.localMsg[msgId] = {};
		delete this.localMsg[msgId];
	}

	function updateMsg(msgId, shoutId, status, overwriteShout)
	{
		if((overwriteShout)&&(overwriteShout != false)) {
			overwriteShout = true;
		}
		
	
		if(this.localMsg[msgId]) {
			if(shoutId) { 
				if(overwriteShout == false) {
					//We only want to set if it doesn't exist
					if(!this.localMsg[msgId].shoutId) {
						this.localMsg[msgId].shoutId = shoutId;
					}
				} else {
					this.localMsg[msgId].shoutId = shoutId;
				}
			}
			if(status) {
				this.localMsg[msgId].status = status;
			}
		}

	}


	function deactivateMsg(msgId)
	{
		//Remove message from server side
		this.localMsg[msgId].status = "deactivate";
			
		this.processEachMsg();
	}

	
	function reactivateMsg(msgId)
	{
		//Check if in deactivating state
		if(this.localMsg[msgId]) {
			if(this.localMsg[msgId].status) {
				if(this.localMsg[msgId].status == 'deactivate') {
					this.localMsg[msgId].status = 'restarting';
					this.localMsg[msgId].shouted = $('#shouted').val();
					this.processEachMsg();		//start again
				}
			}
		}
	}
	


	function deactivateAll()
	{
		//Remove all outstanding messages
		$.ajaxSetup({async:false});		//Since we're closing down the window, we should be able to process all
																		//messages syncronously just in case the browser window has been closed

		var mythis = this;
		$.each(mythis.localMsg, function(key, value) {
			mythis.localMsg[key].status = "deactivate"; 
		});
		
		//Now resend all outstanding messages
		this.processEachMsg();
		
		$.ajaxSetup({async:true});		//Coming back out 

	
	}

	function processEachMsg()
	{
		//Loop through each message in the array
		var mythis = this;
		$.each(mythis.localMsg, function(key, value) {
			if(value.status == "deactivate") {
				//Start the deactivate process if we know the id
				if(value.shoutId) {
					var myShoutId = value.shoutId;
					var myKey = key;
					
					var thisThis = mythis;
					$.ajax({			//Note: we cannot have a timeout on this one. Otherwise
										//it could potentially error out if the data arrives later
						dataType: "jsonp",
						crossDomain: true,
						url: ssshoutServer + "/de.php?callback=?",
						data: {
							mid: value.shoutId,
							passcode: commentLayer,
							just_typing: 'on'
						},
						success: function(response){ 
							var results = response;
							refreshResults(results);
						},
						error: function (jqXHR, textStatus, errorThrown) {
									
        					$("#warnings").html(lsmsg.msgs[lang].lostConnection);
							$("#warnings").show();
							
							//Process messages again in 10 seconds
							setTimeout(function() {
								mg.processEachMsg();
							}, 10000);
						}
					});
					
				
				}
			} else {

				if(value.status == "requestId") {
					//Call for a new shoutId
					mythis.localMsg[key].status = "typing";

					$('#typing-now').val("on");
					$('#message').val(value.shouted); 
					$('#msg-id').val(key);		
					$('#shout-id').val("");
					
					submitShoutAjax(whisper, false, key);	//false for typing
					
				} else {
					//Typing or waiting for completion
					if(value.typing == "off") {

						if((value.status != "complete")&&
						   (value.status != "sending")) {  		
						   //  So either: "committed", "restarting",  "typing", "gotid", "lostid"
					
							//Check if we have our id yet
							if(value.shoutId) {
								//Ready to send
						
								$('#typing-now').val('off');
								$('#message').val(value.shouted);
								$('#msg-id').val(key);
								$('#shout-id').val(value.shoutId);
								submitShoutAjax(value.whisper, true, key);	//true for commit
								mythis.localMsg[key].status = "sending";
							} else {
								if((value.status == 'lostid')||
								   (value.status == 'committed')) {
									//OK, we entered something, it timed-out on the server or some other error,
									//so we can try to commit the whole message now as a new server message anyway
									$('#typing-now').val('off');
									$('#message').val(value.shouted);
									$('#msg-id').val(key);
									$('#shout-id').val('');		//a blank id
									submitShoutAjax(value.whisper, true, key);	//true for commit
									mythis.localMsg[key].status = "sending";
						
								}
							}

						} else {
							//Either 'complete' or 'sending'
							if(value.status == "complete") {
								mythis.finishMsg(key);
							}
						}
						
					} else {
					
						if(value.shoutId) {
								//Ready to restart
								if(value.status == "restarting") {
									$('#typing-now').val('on');
									$('#message').val(value.shouted);
									$('#msg-id').val(key);
									$('#shout-id').val(value.shoutId);
									submitShoutAjax(value.whisper, false, key);	//false for commit
									mythis.localMsg[key].status = "typing";
								}
						}
					}

				}
			}

		});

	}
	
	this.newMsg = newMsg;
	this.commitMsg = commitMsg;
	this.deactivateAll = deactivateAll;
	this.reactivateMsg = reactivateMsg;
	this.deactivateMsg = deactivateMsg;
	this.updateMsg = updateMsg;
	this.finishMsg = finishMsg;


	this.processEachMsg = processEachMsg;
}


function registerNewKeypress()
{
	if(typingTimer) {
		clearTimeout(typingTimer);
	}		//Extend the timer

	var myMsgId = mg.localMsgId;
	typingTimer = setTimeout(function() { 
  		//Delete the typing message (or rather deactivate it)
		mg.deactivateMsg(myMsgId);

	}, 10000);	//30000);

}

//Global msg
var mg = new msg();


$(document).ready(function() {
			var email = getCookie("email");
			var yourName = getCookie("your_name");
			var password = getCookie("your_password");
			var setLang = getCookie("lang");
			if(setLang) {
			    lang = setLang;			
			}
			var screenWidth = $(window).width();
			var screenHeight = $(window).height();
			
			
			//Recieve from parent
			if (window.addEventListener) {
			  window.addEventListener('message', function (e) {
					receiveMessage(e.data);
			  });
			}
			else { // IE8 or earlier
			  window.attachEvent('onmessage', function (e) {
					receiveMessage(e.data);
			  });
			}	
						
			
			refreshLoginStatus();
			
			ssshoutHasFocus = true;
			doLoop();
				
				$('#comment-show-password').click(function() {
					$("#comment-password-vis").slideToggle();
					
				});
				
				$('#comment-user-code').click(function() {
						//Show the user's ip/code
						
					   $.ajax({
							url: ssshoutServer + '/confirm.php?callback=?', 
							data: "usercode=true&passcode=" + commentLayer,
							crossDomain: true,
							dataType: "jsonp"
						}).done(function(response) {
							var msg = 'myMachineUser: ' + response.thisUser;
							$("#user-id-show-set").html(msg);
							$("#user-id-show").toggle();
							
						
							$("#group-users").val(response.layerUsers);
							$("#group-user-count").html(response.layerUserCount);
							$("#group-users-form").toggle();
							$("#subscribers-limit-form").toggle();
							$("#set-forum-password-form").toggle();
							$("#set-forum-title-form").toggle();
						});
				});
				
				$('#shouted').bind('paste',function() {
				 	//Entered a paste operation. Note this wouldn't be detected by a js keypress ordinarily but it does pretty much what the keypress does		
				 	
				 	// Short pause to wait for paste to complete
					setTimeout( function() {
        
        
						//Register that we have started typing
						if(currentlyTyping == false) {
							currentlyTyping = true;
							mg.newMsg(true);  //start typing private message
							registerNewKeypress();
						
						} else {
					
					        mg.reactivateMsg(mg.localMsgId); //if it was deactivated
							registerNewKeypress();
					
						}
				
					}, 100); //end set timeout

				});
				
				$('#shouted').keyup(function(evt) {
					
					evt = evt || window.event;
 					var keyCode = evt.keyCode;

					
         			if((keyCode === 13)||(keyCode === 10)) {
						    //If a return, override the submit not the key. On iphone return is 10
						    				    
						    return mg.commitMsg(sendPublic);

       				}
					
					
					//Register that we have started typing
					if(currentlyTyping == false) {
						currentlyTyping = true;
						mg.newMsg(true);  //start typing private message
						registerNewKeypress();
						
					} else {
						
						//Already typing - wait until status is off again before swtching back on 
						mg.reactivateMsg(mg.localMsgId); //if it was deactivated
						registerNewKeypress();
					
					}
				});
							
				
				$('#chat-input-block').append('<input' + ' type="hidden" ' + 'name="cs" ' + ' value="'+ cs + '">');
				
				

				


		
		});



function whisper(whisper_to, targetName, priv, socialNetwork)
{
   if(typeof(priv) != "undefined") {
      
   		if((priv === false)||(priv == 0)) {
		      //Via a social network - still public. TODO change colour of button?
		 	  whisperOften = whisper_to;		//set global
			  $('#public-button').html("Public to " + targetName);
		 	  $('#private-button').hide();
	          $('#public-button').show();
		 	  
		 	  //Show the private option on the link
			  $('#private-public-link').html(goPrivateMsg);
		 
		      sendPublic = true;
		      shortCode = socialNetwork;
		      publicTo = targetName;
		} else {
		    whisperOften = whisper_to;		//set global
	        $('#private-button').html("Send to " + targetName);
	        $('#private-button').show();
	        $('#public-button').hide();
            sendPublic = false;
            shortCode = "";
            publicTo = "";
            $('#private-public-link').html(goPublicMsg);
		   
		}
     
   } else {
   
      whisperOften = whisper_to;		//set global
	  $('#private-button').html("Send to " + targetName);
	  $('#private-button').show();
	  $('#public-button').hide();
      sendPublic = false;
      shortCode = "";
      publicTo = "";
      $('#private-public-link').html(goPublicMsg);
   
   }
   
}




function refreshLoginStatus()
{
	setTimeout(function(){		//Give ourselves a fraction of a second to wait for sessions to be written on another node
					
		//Update the display after a login via an AJAX call
		var data = $('#options-frm').serialize();

			 $.ajax({
					url: ssshoutServer + '/logged-status.php?callback=?', 
					data: data,
					crossDomain: true,
					dataType: "jsonp"
				}).done(function(response) {
					$("#subscribe-button").html(", " + response.subscribe);
					$("#logged-status").html(response.loggedIn);
					$("#sub-toggle").html(response.subscribeToggle);
					$('#email-explain').hide();		//Blank off messages
				});
	}, 100);

}


function set_options_cookie() {

	//Show a waiting graphic
	$("#comment-messages").html("<img src=\"" + ssshoutServer + "/images/ajax-loader.gif\" width=\"16\" height=\"16\">");
	$("#comment-messages").show();

	//See http://stackoverflow.com/questions/4901633/how-to-store-other-languages-unicode-in-cookies-and-get-it-back-again
    var yourName = encodeURIComponent($('#your-name-opt').val());
    var email = encodeURIComponent($('#email-opt').val());
    var phone = encodeURIComponent($('#phone-opt').val());
    
    var sendNewUserMsg = true;
   
    if(yourName == "") {
    	yourName = ""; 
    	document.cookie = 'your_name=' + yourName + '; path=/; expires=' + cookieOffset() + ';';
    } else {
    	document.cookie = 'your_name=' + yourName + '; path=/; expires=' + cookieOffset() + ';';
    }
    $('#name-pass').val(yourName);	//Set the form
   
    
    if(email == "") {
    	sendNewUserMsg = false;
    	email = "";
    	document.cookie = 'email=' + email + '; path=/; expires=' + cookieOffset() + ';';
    } else {
    	document.cookie = 'email=' + email + '; path=/; expires=' + cookieOffset() + ';';
    }
    $("#email").val(email);		//Set the form
    
    
    if(phone == "") {
    	phone = '';
    	document.cookie = 'phone=' + phone + '; path=/; expires=' + cookieOffset() + ';';
    } else {
    	document.cookie = 'phone=' + phone + '; path=/; expires=' + cookieOffset() + ';';
    }
    $("#phone").val(phone);		//Set the form
    
    //Check if we are trying to check against a password
    var forumPass = $('#forumpass').val();
    if(forumPass != "") {    	
    	$("#forumpasscheck").val(forumPass);		//Set the form
    }
    
    
    var data = $('#options-frm').serialize();
    
   
    
    
    
    
    $.ajax({
			url: ssshoutServer + '/confirm.php?callback=?', 
			data: data,
			crossDomain: true,
			dataType: "jsonp"
		}).done(function(response) {
			$("#comment-messages").val("");
			$("#comment-messages").show();
		
			var msg = "";
			var toggle = true;
			var reload = false;
			var timeMult= 1;
			var newLocation = window.location.href;
			
			var mytype = response.split(','); 
			
			//Always hide the advanced section - we need to click to refresh the data 
			$('#group-users-form').hide();
			$('#subscribers-limit-form').hide();
			$('#set-forum-password-form').hide();
			$('#set-forum-title-form').hide();
			$('#user-id-show').hide();
					
		
			switch(mytype[0])
			{
				case "LOGGED_IN":
					
					
					msg = lsmsg.msgs[lang].loggedIn;
					toggle = true;
					$('#comment-logout-text').show();	//show the correct text 
					$('#comment-not-signed-in').hide();
					$('#comment-logout').show();	//show the logout button
					refreshLoginStatus();
					
					if(mytype[1]) {
				    	if(mytype[1] === "RELOAD") {
				    		//No need to toggle if we are reloading
				        	toggle = false;
				    	}
			    	} 
			    	
			    	var runApp = false;
					if($('#useapp').is(":checked")) {
						runApp = true;
					}
			    	if(runApp) {
						//Run the app / either install or open the app
						deepLinkApp();
					}
					
				break;
				
				case "FORUM_LOGGED_IN":
				
				 	//Also save any password that you entered for the next login
					keepPassword = $('#password-opt').val();
					if(keepPassword) {
						newLocation = window.location.href + "&pd=" + keepPassword;
					}
				
				
					toggle = false;
					msg = lsmsg.msgs[lang].loggedIn;
					$('#forum-logged-in').html(msg);
					$('#forum-logged-in').show();
					$('#forumpasscheck').val("");
					
					refreshLoginStatus();
				
					
				break;
				
				case 'FORUM_INCORRECT_PASS':
				    msg = lsmsg.msgs[lang].forumPasswordWrong;
				    
				    //Also save the password that you entered for the next login
					keepPassword = $('#password-opt').val();
					if(keepPassword) {
						newLocation = window.location.href + "&pd=" + keepPassword;
					}
				    
					toggle = false;
					$('#forum-logged-in').html(msg);
					$('#forum-logged-in').show();
					
					
					refreshLoginStatus();
				break;
				
				case 'INCORRECT_PASS':		
					msg = lsmsg.msgs[lang].passwordWrong;
					$('#comment-password-vis').show();
					toggle = false;
					refreshLoginStatus();
				break;
				
				case 'STORED_PASS':
					msg = lsmsg.msgs[lang].passwordStored;	
					toggle = true;	
					
					refreshLoginStatus();
				break;
				
				case 'NEW_USER':
				
				    if(sendNewUserMsg == true) {
					    msg = lsmsg.msgs[lang].registration;
					    toggle = true;
					    $('#comment-logout-text').show();	//show the correct text 
					    $('#comment-not-signed-in').hide();		
					    timeMult = 6;
					} else {
					    toggle = true;
					    $('#comment-logout-text').show();	//show the correct text 
					    $('#comment-not-signed-in').hide();	
					}
					
					refreshLoginStatus();
					
					var runApp = false;
					if($('#useapp').is(":checked")) {
						runApp = true;
					}
			    	if(runApp) {
						//Run the app / either install or open the app
						deepLinkApp();
						
					}
				break;
				
				case 'SUBSCRIBED':
					msg = lsmsg.msgs[lang].subscribed;
					refreshLoginStatus();
					
					
					var runApp = false;
					if($('#useapp').is(":checked")) {
						runApp = true;
					}
			    	if(runApp) {
						//Run the app / either install or open the app
						deepLinkApp();
					}
				
				break;
				
				
				case 'SUBSCRIPTION_DENIED':
					msg = lsmsg.msgs[lang].subscriptionDenied;
					$('#comment-password-vis').show();
					toggle = false;
					refreshLoginStatus();
				
				break;
				
				
				default:
					msg = lsmsg.msgs[lang].badResponse + response;
					refreshLoginStatus();
					toggle = false;
				break;
			}
			
			
			
			//show the messages again
			if(toggle == true) {
			    
			    var reloadOpt = false;
			    if(mytype[1]) {
				    if(mytype[1] === "RELOAD") {
				        reloadOpt = true;
				    }
			    } 
			
				//Do toggle, but pause if there is a message
				if(msg == '') {
					//Switch back immediately
					$("#comment-popup-content").toggle(); 
					$("#comment-options").toggle();
					
					//Send message to the parent frame to hide highlight
					var targetOrigin = getParentUrl();		//This is in search-secure
					parent.postMessage( {'highlight': "none" }, targetOrigin );
	
					
					if(reloadOpt == true) {
						setTimeout(function(){		//allow time for other cluster node db writes to process
							window.location.assign(newLocation);
						}, 100);
			            refreshLoginStatus();
			           
			        }
					
				} else {
					$("#comment-messages").html(msg);
					$("#comment-messages").show();
					
				
					
					
					//Pause in here for 3 seconds before switching back to message view
					setTimeout(function(){
							
							$("#comment-messages").hide();
							$("#comment-popup-content").toggle(); 
							$("#comment-options").toggle();
							
							//Send message to the parent frame to hide highlight
							var targetOrigin = getParentUrl();		//This is in search-secure
							parent.postMessage( {'highlight': "none" }, targetOrigin );
	
							
							if(reloadOpt == true) {
								window.location.assign(newLocation);
			            		
			            		refreshLoginStatus();
			                }
							
						}, (500*timeMult));
				
				}
			} else {
				//Don't toggle but if there is a message show it
				$("#comment-messages").html(msg);
				$("#comment-messages").show();
				
				
				
	
	            if(mytype[1]) {
			        //carry out a reload of the page too
			        if(mytype[1] === "RELOAD") {
			        
			        	//Send message to the parent frame to hide highlight
						var targetOrigin = getParentUrl();		//This is in search-secure
						parent.postMessage( {'highlight': "none" }, targetOrigin );
			        
			        	//Give ourselves a fraction of a second (1/10sec) to cope with another cluster node not having written session data
						setTimeout(function(){		//Give ourselves a fraction of a second to wait for sessions to be written on another node
							
							window.location.assign(newLocation);
						}, 100);
						refreshLoginStatus();
	
			        }
			    }
	
			}
			
			
				
		});
    
    

	return false;

}




// Variable to store your files
var files;


// Grab the files and set them to our variable
function prepareUpload(event)
{
  files = event.target.files;
}


function upload() {

 	//TODO: show uploading progress
    
 	$('#uploading-wait').show();
    // Create a formdata object and add the files
    
  	var upload = $('#upload-frm').serializeArray();	
   
    
    
    var delay = 10;		//Initial delay is 0 seconds, but increase this to 2 seconds after the first upload
    var passInId = 0;
    
    //Handle each upload, with a 2 second delay between starting each one
    var eachPhoto = setInterval(function() {
		    		
    		var upload = $('#upload-frm').serializeArray();	
    		var imageData = upload[passInId].value;
    		
    		
    		delay = 2000;		
			var data = new FormData();
			var myFormat = {
				"images[]": [ imageData ]
			}
		
			 $.each(myFormat, function(key, value) {
				data.append(key, value);
			 });
	
	
			$.ajax({
					url: ssshoutServer + '/upload-photo.php', 
					data: data,
					dataType: "json",
					type: 'POST',
					cache: false,
					processData: false, // Don't process the files
					contentType: false // Set content type to false as jQuery will tell the server its a query string request
				}).done(function(response) {
					
					
						
					$('#uploading-wait').hide();
			
			
					if(!response.url) {
						$('#uploading-msg').html(response.msg);
						$('#uploading-msg').show();
			
				
					} else {
						//Now wait for a second while internal images get sent around their network
						setTimeout(function(){ 
						
							//Append the response url to the input box
							//Register that we have started typing
							$('#shouted').val( response.url );
							mg.newMsg(true);  //start typing private message
							mg.commitMsg();
			
							$('#uploading-msg').html("");
							$('#uploading-msg').hide();
							$("#comment-popup-content").show(); 
							$("#comment-upload").hide(); 
							//Send message to the parent frame to hide highlight
							var targetOrigin = getParentUrl();		//This is in search-secure
							parent.postMessage( {'highlight': "none" }, targetOrigin );
						
						}, 1500);
					}

			
				
				});

			passInId ++;
			if(passInId == upload.length) {
				clearInterval(eachPhoto);
			}
	},delay);
    

	return false;

}


function removeMessageDirect(messageId)
{
	var thisMessageId = messageId;
	var successDeletion = false;
	//TODO: countdown to prevent infinite attempts?

	
	var ajaxCall = {			//Note: we cannot have a timeout on this one. Otherwise
			//it could potentially error out if the data arrives later
		dataType: "jsonp",
		crossDomain: true,
		url: ssshoutServer + "/de.php?callback=?",
		data: {
			mid: messageId,
			passcode: commentLayer,
			just_typing: 'on'
		},
		success: function(response2){ 
			var results2 = response2;
			successDeletion = true;
			refreshResults(results2);
		},
		error: function (jqXHR, textStatus, errorThrown) {

			$("#warnings").html(lsmsg.msgs[lang].lostConnection);
			$("#warnings").show();
			removeMessageDirect(thisMessageId);
			
		}
	};
	
	setTimeout(function() {
			
		//Warn the user
		if(successDeletion == false) {
			removeMessageDirect(thisMessageId);
		}

	}, 10000);  //After 10 seconds reprocess the deletion attempt
	
	$.ajax(ajaxCall);
	
	return;
}




function submitShoutAjax(whisper, commit, msgId)
{
	
	if(commit == true) {			//if we're commiting, not typing	
		if(whisper == true) {
			$('#whisper_to').val(mg.localMsg[msgId].whisperOften);
		} else {
			$('#whisper_to').val("");		//clear back
	
		}
		$('#whisper_site').val(whisperSite);		//this is the master version from the website
	}
	
	
	if((mg.localMsg[msgId].shouted)&& (mg.localMsg[msgId].shouted!='') && (mg.localMsg[msgId].shouted!='\n')) {
		
		if(window.location.href) {
			var str = encodeURIComponent(window.location.href);
			$('#remoteurl').val(str);
		}
	
		if(mg.localMsg[msgId].shortCode) {
		   $('#short-code').val(mg.localMsg[msgId].shortCode);
		   $('#public-to').val(mg.localMsg[msgId].publicTo);
		} else {
		   //Note a bit slow on every request?
		   $('#short-code').val('');
		   $('#public-to').val('');
		}
		
		
		//Clear any removal to disable after a certain length of time
		if(commit == true) {
				//If we clicked a commit button
			
				//Check if we are still waiting on the previous shout_id
				clearTimeout(typingTimer);
		
		}
		
		var data = $('#comment-input-frm').serialize();
		var mycommit = commit;
		var myMsgId = msgId;
		var myShoutId = $('#shout-id').val();
		var erroredOut = false;
		var aSuccess = false;
		
		//Track requests
		mg.currentRequestId ++;
		var requestId = mg.currentRequestId;
		mg.requests[requestId] = { 
										aSuccess: false,
										erroredOut: false
		 						};		//Create the object
		
		
		var ajaxCall = {			//Note: there must not be a timeout here because it is a cross-domain jsonp request,
									//which will trigger an error after the data arrives if past the timeout
			url: ssshoutServer + '/index.php?callback=?', 
			data: data,
			crossDomain: true,
			dataType: "jsonp",		
			success: function(response) {
				
				if((mg.requests[requestId].aSuccess == false)&&(mg.requests[requestId].erroredOut == false)) {
					mg.requests[requestId].aSuccess = true;
					ssshoutHasFocus = true;
			
			
					var results = response;
						
					var oldShoutId = null;
					var newShoutId = null;
					if((mg.localMsg[myMsgId])&&(mg.localMsg[myMsgId].shoutId)) {
						oldShoutId = mg.localMsg[myMsgId].shoutId;						
					} else {
						
						if((myShoutId)&&(myShoutId != '')) {
						    oldShoutId = myShoutId;
						}
					}
					
					if(results.sid) {
						newShoutId = results.sid;
					}
			
			
					if(mycommit == true) {
						//If we clicked a commit button
						if((newShoutId)&&
							(oldShoutId)&&
							(newShoutId != oldShoutId)) {
							//There exists an old 'typing' message that needs to be deleted
							removeMessageDirect(oldShoutId);
						}
						
						refreshResults(results);
			
						//refresh results will fill in the returned id						
						//Overwrite the existing results
						if(newShoutId) {
							//Session results
							mg.updateMsg(myMsgId, newShoutId, "complete");	
						} else {								
							mg.updateMsg(myMsgId, null, "complete");
						}
												
						clearTimeout(myLoopTimeout);		//reset the main timeout
						doLoop();		//Then refresh the main list
					} else {
						//Update screen and get the shout id only
						//Just a push button
																		
						if(!mg.localMsg[myMsgId]) {
							//If it was already processed and then finished, we need to remove this new one
							removeMessageDirect(newShoutId);
						}
						
						//This is excess if the message has already been completed or sent for real	
						if((newShoutId)&&(oldShoutId)&&							
							   (newShoutId != oldShoutId)) {
							   //We already have a shout id. This message should be removed
							   //if status is already complete and is not the same as the current request
						
								//And it must be the current request
								removeMessageDirect(newShoutId);
						} else {
							if(newShoutId) {
								//No shout id already
								refreshResults(results);  //gets sshout id from in here
							}
						}
					
						if((!oldShoutId)&&(newShoutId)) {
							mg.updateMsg(myMsgId, newShoutId, "");	
						}
						
						
											
					
			
					}
			
					//Go ahead and continue processing all messages outstanding
					mg.processEachMsg();
				}
	
		
					
		
			},
			error: function(jqXHR, textStatus, errorThrown) {
				
				if((mg.requests[requestId].aSuccess == false)&&(mg.requests[requestId].erroredOut == false)) {
					
					if(mg.localMsg[myMsgId].status != "complete") {
						//There was no other complete somewhere else
							
						//OK no response
						if(mycommit == true) {
							//Failure to send a message - warn user here.
							mg.requests[requestId].erroredOut = true;		//Only run this once for this request
			
							//Warn the user
							var wrn = lsmsg.msgs[lang].messageQueued;
							wrn = wrn.replace("MESSAGE", mg.localMsg[myMsgId].shouted);
							$("#warnings").html(wrn);
							$("#warnings").show();
							
					
							mg.updateMsg(myMsgId, null, "committed");	//Go back to committed rather than sending, so we will send again. 
												//Note: Don't update the shoutID because we don't have it
		
							//Process messages again in 10 seconds
							setTimeout(function() {
								mg.processEachMsg();
							}, 10000);							
							
			
						} else {
							//Just typing - this is not critical - but we need to let the next commit know to try again with a lostid
							if((requestId == mg.currentRequestId)) {
								//Only if there has been no concluding new commit should we register this timeout lost in space.
								//which means we need to generate a new id
								mg.updateMsg(myMsgId, null, "lostid");
							}
						}
					}
				
				}
				
			
				
			}
		};
		
		
		var thisMyMsgId = myMsgId;
		var thisMycommit = mycommit;

		
		//Check for erroring out after a long 20 sec timeout
		setTimeout(function() {
			
			var myMsgId = thisMyMsgId;
			var mycommit = thisMycommit;
			 
			if(mg.localMsg[thisMyMsgId]) {
				//If the message still exists
				if((mg.requests[requestId].aSuccess == false)&&(mg.requests[requestId].erroredOut == false)) {
					//And it isn't complete or lost				
					if(mycommit == true) {
						ajaxCall.error();
					}
				}
			}
		}, 20000);		
		
		setTimeout(function() {
			if(mg.localMsg[thisMyMsgId]) {
				//If the message still exists
				if((mg.requests[requestId].aSuccess == false)&&(mg.requests[requestId].erroredOut == false)) {
					//Warn the user
					if(mycommit == true) {
						var wrn = lsmsg.msgs[lang].messageQueued;
						wrn = wrn.replace("MESSAGE", mg.localMsg[myMsgId].shouted);
						$("#warnings").html(wrn);
						$("#warnings").show();
					}
				}
			}
		}, 3000);  //After 3 seconds process a timeout warning
				
		$.ajax(ajaxCall);
		
	} else {
	
		//Show warning for blank message sent
		$("#warnings").html(lsmsg.msgs[lang].blankMessage);
		$("#warnings").show();
		$("#shouted").val('');	//Clear off
		
	}
	
	
	return false;

}


function closeSingleMsg()
{
	//Close the single message form
	$('#comment-single-msg').hide();
							
	$("#comment-popup-content").show();
	return false;
}

function hideSingleMsg(id)
{
	//Hide the message and then refresh the results, and close the form 
	closeSingleMsg();
	$.getJSON(ssshoutServer + "/de.php?callback=?", {
						mid: id,
						passcode: commentLayer 
					}, function(response){ 
						var results = response;
						refreshResults(results);
					});
	return false;
}



function displaySingleMsg(msgId, localId)
{
	var content = '<div style="position: relative; float:right;"><a href="javascript:" onclick="return closeSingleMsg();"><img width="16" style="margin:20px;" src="images/close.svg"></a></div><br/>' + globResults.res[localId].text + '<br/><br/><a class="comment-msg-button" href="javascript:" onclick="return hideSingleMsg(' + msgId + ');"><img width="48" src="images/bin.svg"></a><span id="single-msg-buttons"></span><script>$.getJSON(ssshoutServer + "/single-msg-buttons.php?callback=?", { mid: ' + msgId + ', passcode: commentLayer },function(response){ var results = response; $("#single-msg-buttons").html(results); });</script>';
	$('#comment-single-msg').html(content);

	$("#comment-popup-content").hide();
	$("#comment-emojis").hide();
	$("#comment-options").hide();
	$("#comment-upload").hide();
	$('#comment-single-msg').show();
	
	return false;
}

function refreshResults(results)
{
	globResults = results; //Get a pointer to these results
	
	if(results.res) {
		if(results.res.length) {
				$("#warnings").hide();		//All good - no warnings
			
			
				var newLine = "";
			
			
				newLine = "<table class=\"table table-striped\" style=\"table-layout: fixed;\">";
			
	 			for(var cnt=0; cnt<results.res.length; cnt++) {
	 				
	 				if(results.res[cnt].whisper == true) {
	 					var priv = "title=\"Private\" class=\"info backmsg\"";
	 				} else {
	 				
	 					var priv = "class=\"backmsg\"";
	 				}
	 			
	 				if(results.res[cnt].text) {
	 					//Used to be: width="65%"
	 					var line = '<tr ' + priv + ' data-id=\"' + results.res[cnt].id + ',' + cnt + '\"><td class=\"comment-msg-td\" width=\"65%\">' + family(results.res[cnt].text) + '</td><td class=\"comment-ago-td\"><div class=\"comment-ago-text\">' + results.res[cnt].ago + '</div></td></tr>';
		 				newLine = newLine + line;
		 				
		 				
		 			}
			
				}
				
				if((results.res.length >= showMore)&&(records <= showMore)) {		//we need to show more if there are more	
					var line = '<tr><td class=\"comment-msg-td\" width=\"65%\">&nbsp;</td><td class=\"comment-ago-td\"><div class=\"comment-ago-text\"><a href="javascript:" onclick=\"records=500;\">' +lsmsg.msgs[lang].more + '</a></div></td></tr>';
			 		newLine = newLine + line;
			 	}
			
				newLine = newLine + '</table>';
				$('#comment-prev-messages').html(newLine);
				
				$('.backmsg').click(function (e) {
					if((e.target.nodeName == 'TD')
						|| (e.target.nodeName == 'td')
						|| (e.target.nodeName == 'DIV')
						|| (e.target.nodeName == 'div')
						) {
							//We know it is a background element - not a link
							var thisdat = $(this).attr("data-id").split(","); 
							displaySingleMsg(thisdat[0], thisdat[1]);
					} else {
						
					}
				});
		}
	}
	
	if(results.ses) {
		//Session results
		$('#ses').val(results.ses);
  	
      	//Set the cookie also so that when we come back we will have same user
      	var ses = results.ses;
      	document.cookie = 'ses=' + ses + '; path=/; expires=' + cookieOffset() + ';'; //Thu,31-Dec-2020 00:00:00 GMT
  
	}
	
	if(results.title) {
		//There is a new title for the forum. Update the parent frame.
		var targetOrigin = getParentUrl();		//This is in search-secure
		parent.postMessage( {'title': results.title }, targetOrigin );
	
	}
	
	if(results.sid) {
		//Session results
		
		mg.updateMsg(results.lid, results.sid, null, false);		//Status is not changing, but 'false' means if it exists already, don't overwrite
	
	}

}


function doSearch()
{
	//Port is set in search-secure
	
	if(portReset == false) {
		//OK - this is the first one after a logout = we can reset if after this
		portReset = true;	
	}
	
	if(granted == false) {
		return;
	
	}
	
	
	if((readPort)&&(readPort != null)&&(readPort != "")&&(!port)) {
		//Use an alternative port for reading - useful by the Loop-server-fast plugin. Note: readURL will overwrite this if set.
		var serv = assignPortToURL(ssshoutServer, readPort);
	} else {
	
		var serv = assignPortToURL(ssshoutServer, port);
	}
	
	if((readURL)&&(readURL != null)&&(readURL != "")&&(!port)) {
		//Use an alternative URL for reading - useful by the Loop-server-fast plugin
		var serv = readURL;	
	}
	
	var callResults = false;		//flag for returned or not
	
	var ajaxCall = {
  		dataType: "jsonp",
  		contentType: "application/json",
  		url: serv + "/search-chat.php?callback=?",			
  		data: {
					lat: $('#lat').val(),
					lon: $('#lon').val(),
					passcode: commentLayer,
					units: 'mi',
					volume: 1.00,
					records: records,
					lang: lang,
					whisper_site: whisperSite,
					sessionId: $('#ses').val(),
					general: $('#general-data-hidden').val(),
					subdomain: subdomain
											
		},
		success: function(response){ 
			 	callResults = true;		//flag this as having returned
			 	if(portReset == true) {
			 		port = "";			//reset the port if it had been set	
			 	} else {
			 		//This was still a residual reset middway when we clicked logout
			 		//OK now we can reset the port next time we call - this is particularly after a logout is called
			 		portReset = true;
			 		return;		//Don't refresh the results on this request
			 		
			 			
			 	}	  			
				
				
				
				var results = response;
				refreshResults(results);
				
				
		},
        error: function (jqXHR, textStatus, errorThrown) {
							
        	$("#warnings").html(lsmsg.msgs[lang].lostConnection);
			$("#warnings").show();
        }
    }
    
    
	setTimeout(function() {	
		if(callResults == false) {
			ajaxCall.error();
		}
		
	}, 3000);		//After 3 seconds process a timeout
			
	$.ajax(ajaxCall);
    

}


cs += 9585328;

function doLoop()
{
	
	if((ssshoutHasFocus == true)&&(granted == true)) {
		//Only do searches when have focus
		doSearch();
	} 
	
	
	
	myLoopTimeout = setTimeout(function() {	doLoop(); }, ssshoutFreq);  //Continue loop no matter what
}

cs += 124856;
				
function family(string)
{
	if(string) {
		string = string.replace(/f+u+c+k+/gi, "****");
		string = string.replace(/s+h+i+t+/gi, "****");
		string = string.replace(/c+o+c+k+/gi, "****");
		string = string.replace(/d+i+c+k+/gi, "****");
		string = string.replace(/p+e+n+u+s+/gi, "*****");
		string = string.replace(/p+e+n+i+s+/gi, "*****");
		string = string.replace(/a+r+s+e+h+o+l+e+/gi, "********");
		string = string.replace(/b+a+r+s+t+a+r+d+/gi, "********");
		string = string.replace(/v+a+g+i+n+a+/gi, "********");
		string = string.replace(/t+i+t+s+/gi, "********");
		string = string.replace(/t+e+s+t+i+c+a+l+s+/gi, "********");
		string = string.replace(/w+i+l+l+i+e+/gi, "********");
		string = string.replace(/b+i+t+c+h+/gi, "*****");
		return string;
	} else {
		return '';
	}

}

cs += 9484320;


function beforeLogout(cb) {
    //This is called before logout.php is called
    
    //Reset the email/pass
    $('#email-opt').val('');
    $('#your-name-opt').val('');
    $('#password-opt').val('');
    $('#phone-opt').val('');
    $('#name-pass').val('');
    
    $('#group-users-form').hide();
    $('#subscribers-limit-form').hide();
    
	$('#set-forum-password-form').hide();
	$('#set-forum-title-form').hide();
	$('#user-id-show').hide();

 
    
    //Clear out the local cookies
    document.cookie = "your_name=deleted; path=/; expires=Thu, 01 Jan 1970 00:00:01 GMT;";
    document.cookie = "email=deleted; path=/; expires=Thu, 01 Jan 1970 00:00:01 GMT;";
    document.cookie = "phone=deleted; path=/; expires=Thu, 01 Jan 1970 00:00:01 GMT;";
    document.cookie = "your_password=deleted; path=/; expires=Thu, 01 Jan 1970 00:00:01 GMT;";

    
    cb();

}

function logout() {
	//This is called after the call to logout.php is complete
	$('#comment-logout-text').hide();	//show the correct text 
	$('#comment-not-signed-in').show();
	$('#ses').val('');  //also sign out the current sess
 
    

    $('#comment-prev-messages').html('');   //remove any existing messages
   

	portReset = false; 
	port=initPort;
	
	//And run a search
	doSearch();
	return;
}


var myEvent = window.attachEvent || window.addEventListener;
var chkevent = window.attachEvent ? 'onbeforeunload' : 'beforeunload'; /// make IE7, IE8 compitable
//iphone is 'pagehide' event, and blackberry is 'onunload'

myEvent(chkevent, function(e) { // For >=IE7, Chrome, Firefox


	//get requests syncronously in deactivate all
	mg.deactivateAll();
	
  return;	
});

myEvent("pagehide", function(e) { // For Iphones/Ipads

	mg.deactivateAll();
	
  return;	
});

myEvent("onunload", function(e) { // For Blackberrys

	mg.deactivateAll();
	
  return;	
});


			

