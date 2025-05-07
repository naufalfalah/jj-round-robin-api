const firebaseConfig = {
    apiKey: "AIzaSyAkNawkQam5CJDZtpAQjaFWRWn1ZWOw6I4",
    authDomain: "jomejourney.firebaseapp.com",
    projectId: "jomejourney",
    storageBucket: "jomejourney.appspot.com",
    messagingSenderId: "199637720431",
    appId: "1:199637720431:web:7464b402b0184443091c9a",
    measurementId: "G-0XD7BZ52X0"
  };

firebase.initializeApp(firebaseConfig);
const messaging = firebase.messaging();

function initFirebaseMessagingRegistration() {
    messaging.requestPermission().then(function () {
        return messaging.getToken()
    }).then(function(token) {

        if (token) {
            sendTokenToServer(token);
        }else{
            setTokenSentToServer(false);
        }

    }).catch(function (err) {
        setTokenSentToServer(false);
        console.log(`Token Error :: ${err}`);
    });
}

initFirebaseMessagingRegistration();


messaging.onMessage((payload) => {
    //console.log("Firebase.js Message Received ",payload);
    // console.log(payload);
    try {
        const title = payload.data.title;
        const options = {
            body: payload.data.body,
            icon: payload.data.icon,
            // image: payload.data.image,
        };
        new Notification(title, options);
        $.ajax({
            url: payload.data.notify_route,
            type: 'get',
            dataType: 'JSON',
            success: function(res) {
                if (res.count > 0) {
                    $("#notification_count_badge").removeClass('d-none').html(res.count);
                    $('.header-notifications-list').html(res.view_data);
                }
            },
        });

    } catch (err) {
        console.error('Error displaying notification:', err);
    }
});

// send token to server where it is used for sending notification

function sendTokenToServer(token){
    // first check if we already send it or not
    if (!isTokenSentToServer()) {
        console.log('Sending token to server ....');
        // if token is successfully sent to the server
        // then set setTokenSentTOServer to true

        $.ajax({
            type: "POST",
            url: setTokenUri,
            data: {
                '_token':ct,
                'device_token':token
            },
            dataType: "json",
            error: function (jqXHR, textStatus, errorThrown) {
                console.log(jqXHR, textStatus, errorThrown);
                setTokenSentToServer(false);
            },
            success: function (res) {
                setTokenSentToServer(true);
            }
        });

    }else{
        console.log('Token already available in the server');
    }
}

function isTokenSentToServer() {
    return window.localStorage.getItem('sentToServer') == '1';
}

// we need to set the value of "sendTokenToServer" to true in the localStorage
// so if we are sending second time, we will check for localStorage
function setTokenSentToServer(sent) {
    window.localStorage.setItem('sentToServer', sent ? '1' : '0');
}
