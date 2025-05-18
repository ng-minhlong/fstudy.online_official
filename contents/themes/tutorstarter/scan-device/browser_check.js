// browser_check.js
function detectBrowser() {
    const userAgent = navigator.userAgent.toLowerCase();
    let browser = "Unknown Browser";
    let browserIcon = "🌐";
    let browserVersion = "N/A";

    // Detect browser and version
    if (userAgent.includes('coccoc')) {
        browser = "Cốc Cốc";
        browserIcon = "🦊";
        browserVersion = userAgent.match(/coccoc\/([\d.]+)/)?.[1] || browserVersion;
    } else if (userAgent.includes('opera') || userAgent.includes('opr/')) {
        browser = "Opera";
        browserIcon = "🎭";
        browserVersion = userAgent.match(/(?:opera|opr)[\s/]([\d.]+)/)?.[1] || browserVersion;
    } else if (userAgent.includes('edg')) {
        browser = "Microsoft Edge";
        browserIcon = "�";
        browserVersion = userAgent.match(/edg\/([\d.]+)/)?.[1] || browserVersion;
    } else if (userAgent.includes('chrome')) {
        browser = "Google Chrome";
        browserIcon = "🟡";
        browserVersion = userAgent.match(/chrome\/([\d.]+)/)?.[1] || browserVersion;
    } else if (userAgent.includes('safari')) {
        browser = "Apple Safari";
        browserIcon = "🍏";
        browserVersion = userAgent.match(/version\/([\d.]+)/)?.[1] || browserVersion;
    } else if (userAgent.includes('firefox')) {
        browser = "Mozilla Firefox";
        browserIcon = "🦊";
        browserVersion = userAgent.match(/firefox\/([\d.]+)/)?.[1] || browserVersion;
    } else if (userAgent.includes('msie') || userAgent.includes('trident/')) {
        browser = "Internet Explorer";
        browserIcon = "�";
        browserVersion = userAgent.match(/(?:msie |rv:)([\d.]+)/)?.[1] || browserVersion;
    }

    // Additional info
    const isMobile = /mobile|android|iphone|ipad|ipod/i.test(userAgent);
    const platform = isMobile ? "Mobile" : "Desktop";

    return {
        name: browser,
        icon: browserIcon,
        version: browserVersion,
        platform: platform,
        userAgent: navigator.userAgent
    };
}

function checkBrowser() {
    const browserInfo = detectBrowser();
    
    console.log('📊 Thông tin trình duyệt:');
    console.log(`- Trình duyệt: ${browserInfo.icon} ${browserInfo.name}`);
    console.log(`- Phiên bản: ${browserInfo.version}`);
    console.log(`- Nền tảng: ${browserInfo.platform}`);
    console.log(`- User Agent: ${browserInfo.userAgent}`);
    
    return browserInfo;
}

// Run immediately when loaded
checkBrowser();