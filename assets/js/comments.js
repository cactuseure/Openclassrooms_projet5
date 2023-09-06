window.addEventListener("DOMContentLoaded", () => {
const replyButtons = document.querySelectorAll(".reply_btn");

setTimeout(function () {
replyButtons.forEach((button)=>{
button.addEventListener('click', () => {
let commentId = button.dataset.commentId;
let div = document.getElementById('div_comment_' + commentId);
if (div) {
div.hidden = !div.hidden
} else {
console.error("div introuvable pour l'élément avec l'ID " + commentId + ".");
}
});
});
}, 2000);
});
