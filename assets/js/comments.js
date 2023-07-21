window.addEventListener("DOMContentLoaded", (event) => {
    const replyButtons = document.querySelectorAll('.reply_btn');
    const delayInMilliseconds = 2000; // 1 second

    setTimeout(function() {
        replyButtons.forEach((button) => {
            let commentId = button.dataset.commentId;
            if (commentId !== null) {
                let form = document.getElementById('form_comment_' + commentId);
                if (form) {
                    let textarea = form.querySelector('div.tox.tox-tinymce');
                    if (textarea) {
                        textarea.hidden = true;
                    } else {
                        console.error("Textarea introuvable pour l'élément avec l'ID " + commentId + ".");
                    }

                    let btn = form.querySelector('button.btn.primary');
                    if (btn) {
                        btn.style.display = 'none';
                    } else {
                        console.error("btn introuvable pour l'élément avec l'ID " + commentId + ".");
                    }
                } else {
                    console.error("Form introuvable pour l'élément avec l'ID " + commentId + ".");
                }
            }
        });

        replyButtons.forEach((button) => {
            button.addEventListener('click', () => {
                let commentId = button.dataset.commentId;
                let form = document.getElementById('form_comment_' + commentId);
                let textarea = form.querySelector('div.tox.tox-tinymce');
                let btn = form.querySelector('button.btn.primary');
                if (btn.style.display==='none'){
                    btn.style.display='inline-flex';
                }else {
                    btn.style.display='none';
                }
                textarea.hidden = !textarea.hidden;
                if (!textarea.hidden){
                    form.classList.add('show')
                }else {
                    form.classList.remove('show')
                }
            });
        });

    }, delayInMilliseconds);
});
