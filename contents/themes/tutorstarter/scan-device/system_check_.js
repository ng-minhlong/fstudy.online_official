function getOS() {
    const userAgent = navigator.userAgent || navigator.vendor || window.opera;
    let os;
    
    if (/windows/i.test(userAgent)) {
        os = "Windows";
    } else if (/macintosh|mac os x/i.test(userAgent)) {
        os = "Macintosh";
    } else if (/linux/i.test(userAgent)) {
        os = "Linux";
    } else if (/android/i.test(userAgent)) {
        os = "Android";
    } else if (/iphone|ipad|ipod/i.test(userAgent)) {
        os = "iOS";
    } else {
        os = "Unknown OS";
    }
    
    console.log("Thiết bị hiện tại:", os);
    return os;
}