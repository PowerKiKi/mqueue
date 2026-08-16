function formatRelativeToNow(targetDate, lang) {
    const rtf = new Intl.RelativeTimeFormat(lang, {
        numeric: "auto",
    });

    // Calculate difference in milliseconds
    const msPerDay = 24 * 60 * 60 * 1000;
    const msPerWeek = msPerDay * 7;
    const msPerMonth = msPerDay * 30;
    const msPerYear = msPerMonth * 12;

    // Get time difference from now
    const elapsed = targetDate - Date.now();

    // Choose the best fit unit depending on the delta
    if (Math.abs(elapsed) < 60 * 1000) {
        return rtf.format(Math.round(elapsed / 1000), 'second');
    } else if (Math.abs(elapsed) < 60 * 60 * 1000) {
        return rtf.format(Math.round(elapsed / (60 * 1000)), 'minute');
    } else if (Math.abs(elapsed) < msPerDay) {
        return rtf.format(Math.round(elapsed / (60 * 60 * 1000)), 'hour');
    } else if (Math.abs(elapsed) < msPerWeek) {
        return rtf.format(Math.round(elapsed / msPerDay), 'day');
    } else if (Math.abs(elapsed) < msPerMonth) {
        return rtf.format(Math.round(elapsed / msPerWeek), 'week');
    } else if (Math.abs(elapsed) < msPerYear) {
        return rtf.format(Math.round(elapsed / msPerMonth), 'month');
    } else {
        return rtf.format(Math.round(elapsed / msPerYear), 'year');
    }
}

function timeAgo() {
    const lang = document.documentElement.getAttribute('lang');
    document.querySelectorAll('.timestamp').forEach(element => {
        const date = new Date(element.textContent);
        element.textContent = formatRelativeToNow(date, lang);
    })
}

document.addEventListener("DOMContentLoaded", timeAgo);
