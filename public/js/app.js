document.addEventListener("DOMContentLoaded", function () {
    const emojiModal = document.getElementById("modalEmoji");
    const emojiButtons = $('.emoji'); // Selecciona todos los botones de emoji
    const comments = document.querySelectorAll(".comment");
    const commentModal = document.getElementById('commentModal');
    const emojis = $('#modalEmoji').find('p');
    const commentInput = $('.comment-input');
    const messages = document.getElementById('messages');
    const imagePost = document.getElementById('postInput');
    const bodyForm = document.getElementById('body-form');
    const nextButton = document.getElementById('nextButton');
    const filterDiv = document.getElementById('filters');
    const imageDiv = document.getElementById('imageDiv');
    const saturacionInput = document.getElementById("saturacion");
    const contrasteInput = document.getElementById("contraste");
    const tituloCabecera = document.getElementById('tituloCabecera');
    const descriptionInput = document.getElementById('description');
    const filtros = document.getElementById('filtros').children;

    // Variables para los filtros
    let saturacion = 100;
    let contraste = 100;

// Función para crear un Blob a partir de la imagen procesada en el canvas
    function obtenerImagenProcesada() {
        const canvas = document.createElement('canvas');
        const ctx = canvas.getContext('2d');
        const img = imageDiv.firstElementChild; // Imagen cargada

        // Configuramos el tamaño del canvas con el tamaño de la imagen
        canvas.width = img.width;
        canvas.height = img.height;

        // Dibujamos la imagen en el canvas
        ctx.drawImage(img, 0, 0, img.width, img.height);

        // Aplicamos los filtros acumulados
        ctx.filter = filtrosAplicados;

        // Redibujamos la imagen con los filtros aplicados
        ctx.drawImage(img, 0, 0, img.width, img.height);

        // Convertimos el canvas a un Blob (imagen procesada)
        return new Promise((resolve) => {
            canvas.toBlob((blob) => {
                resolve(blob);
            });
        });
    }

    imagePost.addEventListener('change', (ev) => {
        const file = ev.target.files[0];

        // Crear un elemento de imagen y agregarlo al div
        bodyForm.classList.add('hide');
        const img = document.createElement('img');
        img.src = URL.createObjectURL(file);
        img.classList.add('imagenDiv');
        img.onload = () => {
            // Una vez cargada la imagen, establecer el tamaño del canvas
            img.width = 400; // Ajusta según lo necesario
            img.height = 400;
        };
        imageDiv.appendChild(img);

        // Habilitar el botón siguiente
        nextButton.classList.remove('hide');
    });

    Array.from(filtros).forEach(filtro => {
        filtro.addEventListener('click', (ev) => {
            switch (filtro.getAttribute('data-filters')){
                case 'blancoNegro':
                    imageDiv.firstElementChild.style.filter = 'grayscale(100%)';
                    break;
                case 'desenfoque':
                    imageDiv.firstElementChild.style.filter = 'blur(2px)';
                    break;
                case 'sepia':
                    imageDiv.firstElementChild.style.filter = 'sepia(100%)';
                    break;
                case 'invertir':
                    imageDiv.firstElementChild.style.filter = 'invert(100%)';
                    break;
                case 'normal':
                    imageDiv.firstElementChild.style.filter = 'none';
                    break;
            }
        })
        function actualizarFiltros() {
            // Aplica todos los filtros acumulados
            imageDiv.firstElementChild.style.filter = `saturate(${saturacion}%) contrast(${contraste}%)`;
        }

        // Escucha el evento 'input' para saturación
        saturacionInput.addEventListener("input", (ev) => {
            saturacion = ev.target.value; // Actualiza la variable de saturación
            actualizarFiltros();          // Aplica los cambios
        });

        // Escucha el evento 'input' para contraste
        contrasteInput.addEventListener("input", (ev) => {
            contraste = ev.target.value;   // Actualiza la variable de contraste
            actualizarFiltros();           // Aplica los cambios
        });
    })
    nextButton.addEventListener('click', () => {
        nextButton.classList.toggle('editar');
        bodyForm.classList.add('hide');
        filterDiv.classList.remove('hide');
        filterDiv.prepend(imageDiv);
        tituloCabecera.textContent = 'Editar'; // Cambiar el título del header
        filtrosAplicados = imageDiv.firstElementChild.style.filter;
        if(!nextButton.classList.contains('editar')){
            nextButton.classList.add('hide');
            document.getElementById('compartir').classList.remove('hide');
            document.getElementById('filtros').classList.add('hide');
            bodyForm.classList.remove('hide');
            descriptionInput.classList.remove('hide')
            document.getElementById('textoInput').textContent = "Descripcion";
            imagePost.classList.add('hide');
            filterDiv.appendChild(bodyForm);
        }
    });

    // Función para reemplazar el archivo en el input con la imagen procesada
    async function reemplazarArchivoConImagenProcesada() {
        const blob = await obtenerImagenProcesada();

        // Crear un nuevo archivo de tipo imagen
        const processedFile = new File([blob], 'imagenProcesada.jpg', { type: 'image/jpeg' });

        // Crear un nuevo FileList con el archivo procesado
        const dataTransfer = new DataTransfer();
        dataTransfer.items.add(processedFile);

        // Reemplazar el archivo en el input
        imagePost.files = dataTransfer.files;

        // Log para verificar que el archivo ha sido reemplazado correctamente
        console.log(imagePost.files[0]);
    }

// Llamar a esta función cuando se quiera reemplazar la imagen en el input
    document.getElementById('compartir').addEventListener('click', () => {
        reemplazarArchivoConImagenProcesada().then();
    });
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

