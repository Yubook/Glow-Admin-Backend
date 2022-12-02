/* 
importScripts("https://www.gstatic.com/firebasejs/8.6.8/firebase-app.js");
importScripts("https://www.gstatic.com/firebasejs/8.6.8/firebase-messaging.js");


firebase.initializeApp({
  apiKey: "AIzaSyAvIhqc2ddzLBgsi2XcWJMrzl8TrAG0sKE",
  authDomain: "fade-16089.firebaseapp.com",
  projectId: "fade-16089",
  storageBucket: "fade-16089.appspot.com",
  messagingSenderId: "93461071428",
  appId: "1:93461071428:web:d8e4c7e3e542f576309e11",
  measurementId: "G-T5ZY0EW9T6",
});


const messaging = firebase.messaging();
messaging.setBackgroundMessageHandler(function(payload) {
  console.log(
    "[firebase-messaging-sw.js] Received background message ",
    payload
  );
 
  const notificationTitle = "Background Message Title";
  const notificationOptions = {
    body: "Background Message body.",
    //icon: "/itwonders-web-logo.png",
  };

  return self.registration.showNotification(
    notificationTitle,
    notificationOptions
  );
});
 */