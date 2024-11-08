document.addEventListener("DOMContentLoaded", function () {
    const emojiModal = document.getElementById("modalEmoji");
    const emojiButtons = $('.emoji'); // Selecciona todos los botones de emoji
    const comments = document.querySelectorAll(".comment");
    const commentModal = document.getElementById('commentModal');
    const emojis = $('#modalEmoji').find('p');
    const commentInput = $('.comment-input');
    const messages = document.getElementById('messages');
    const imagePost = document.getElementById('postInput');
    imagePost.addEventListener('change', (ev) => {
        console.log(new URL(imagePost.value).pathname);
    })
    // Selecciona todas las imágenes con la clase lazy-load
    const lazyImages = document.querySelectorAll("img.lazy-load");

    // Configura el Intersection Observer
    const observer = new IntersectionObserver((entries, observer) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                // Obtiene la imagen y asigna el src desde el data-src
                const img = entry.target;
                img.src = img.getAttribute("data-src");

                // Remueve data-src para evitar que vuelva a cargarse
                img.removeAttribute("data-src");

                // Deja de observar esta imagen
                observer.unobserve(img);
            }
        });
    }, {
        rootMargin: "0px 0px 0px 0px" // Carga un poco antes de que sea visible
    });

    // Observa cada imagen con lazy loading
    lazyImages.forEach(img => observer.observe(img));


    if (messages) messages.scrollTop = messages.scrollHeight;
    // Desvincula eventos previos para evitar duplicados


    $(commentInput).on('input',(e) => {
        const input = e.currentTarget;
        const parent = $(input).parent();
        const postComment = $(parent).parent();

        const publish = $(postComment).find('.publish');

        if(input.value){
            console.log('hOla')
            publish[0].removeAttribute('disabled');
            console.log(publish[0])
        } else {
            publish[0].setAttribute('disabled','true');
        }

    })
    emojiButtons.unbind();
    emojiButtons.click((e) => {
        e.preventDefault();
        const emojiButton = e.currentTarget; // Almacena el botón actual
        const rect = emojiButton.getBoundingClientRect();
        const scrollTop = window.scrollY || document.documentElement.scrollTop;

        // Posiciona el modal de emojis cerca del botón de emoji
        emojiModal.style.top = `${rect.top + scrollTop - emojiModal.offsetHeight - 100}px`;
        emojiModal.style.left = `${rect.left}px`;

        // Alterna la visibilidad del modal
        emojiModal.classList.toggle("hide");

        e.stopPropagation();
        const parent = $(emojiButton).parent();// Usa emojiButton para el contexto correcto
        const input = parent.find('.comment-input'); // Encuentra el input relacionado

        emojis.off('click'); // Remueve eventos previos en los emojis
        emojis.click((e) => {
            e.preventDefault();
            input.val(input.val() + $(e.target).text()); // Añade el emoji al input
        });
    });

    comments.forEach(button => {
        button.addEventListener('click', (e) => {
            e.preventDefault();
            e.stopPropagation();
            commentModal.classList.toggle('hide');

            if (!commentModal.classList.contains('hide')) {
                document.body.style.overflow = "hidden";
            }
        });
    });

    document.addEventListener("click", (e) => {
        if (!emojiModal.contains(e.target) && !e.target.classList.contains("emoji") && !commentModal.contains(e.target)) {
            emojiModal.classList.add("hide");
            commentModal.classList.add('hide');
            document.body.style.overflow = "auto";
        }
    });

    publicaciones_title = document.getElementById("publicaciones-title");
    guardados = document.getElementById("guardados");

    posts = document.getElementById("publicaciones");
    postGuardados = document.getElementById("publicaciones-guardadas");


    publicaciones_title.addEventListener("click",function (){
        postGuardados.style.display = "none";
        posts.style.display = "grid";
        publicaciones_title.classList.add("active");
        guardados.classList.remove("active");
    })


    guardados.addEventListener("click",function (){
        posts.style.display = "none";
        postGuardados.style.display = "grid";
        publicaciones_title.classList.remove("active");
        guardados.classList.add("active");
    })
});

