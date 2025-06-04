function loadContent(page) {
    const content = document.getElementById('main-content');
    content.innerHTML = "<p>Loading...</p>";

    const xhr = new XMLHttpRequest();
    xhr.open('GET', page, true);
    xhr.onload = function () {
        if (xhr.status === 200) {
            content.innerHTML = xhr.responseText;
        } else {
            content.innerHTML = "<p>Error loading page.</p>";
        }
    };
    xhr.onerror = function () {
        content.innerHTML = "<p>Network error.</p>";
    };
    xhr.send();
}
