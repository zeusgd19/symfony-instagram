window.onload = () => {
    const emojiModal = document.getElementById("modalEmoji");
    const emojiButtons = $('.emoji'); // Selecciona todos los botones de emoji
    const comments = document.querySelectorAll(".comment");
    const commentModal = document.getElementById('commentModal');
    const emojis = $('#modalEmoji').find('p');
    const commentInput = $('.comment-input');
    const messages = document.getElementById('messages');

    $("#formSearchUsers").submit((ev) => {
        ev.preventDefault();

        let username = $("#formSearchUsers").find('#username').val();

        window.location.href = `/search/${username}`
    })

    $(".deleteImage").click((ev) => {
        ev.preventDefault();
        const id = $(ev.currentTarget).data('id'); // Accede al data-id correctamente

        // Muestra un botón o confirma la eliminación
        $(`a[data-id=${id}]`).toggleClass('hide').click((ev) => {
            ev.preventDefault();

            $.get(`/post/delete/${id}`, (response) => {
                if (response.success) {
                    // Si la eliminación es exitosa, elimina el post del DOM
                    $(`#post-${id}`).slideUp(400, function () {
                        $(this).remove(); // Elimina el elemento una vez completada la animación
                    });
                }
            });
        });
    });


    let filtrosAplicados = "";
    $('#enlace-crear').click((e) => {
        e.preventDefault();
    })
    $('#create-post').click(function (e) {
        e.preventDefault();
        document.body.style.overflow = 'hidden';
        // Mostrar la modal
        // Hacer la solicitud AJAX para obtener el formulario
        $.post("/post/new", (response) => {
            // Ruta a tu controlador
            // Insertar el formulario en la modal
            $('#formPost').html(response).removeClass('hide');
            console.log($('#formPost'))
            const nextButton = document.getElementById('nextButton');
            const bodyForm = document.getElementById('body-form');
            const filterDiv = document.getElementById('filters');
            const imageDiv = document.getElementById('imageDiv');
            const saturacionInput = document.getElementById("saturacion");
            const contrasteInput = document.getElementById("contraste");
            const tituloCabecera = document.getElementById('tituloCabecera');
            const descriptionInput = document.getElementById('post_form_description');
            const imagePost = document.getElementById('post_form_photo');
            if (imagePost) {
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
                    console.log('Hola')
                    // Habilitar el botón siguiente
                    nextButton.classList.remove('hide');
                });
            }

            if (nextButton) {
                nextButton.addEventListener('click', () => {
                    const filtros = document.getElementById('filtros').children
                    nextButton.classList.toggle('editar');
                    bodyForm.classList.add('hide');
                    filterDiv.classList.remove('hide');
                    filterDiv.prepend(imageDiv);
                    tituloCabecera.textContent = 'Editar'; // Cambiar el título del header
                    document.getElementById('nextButton').addEventListener('click', () => {
                        reemplazarArchivoConImagenProcesada().then(() => {
                            console.log('Imagen reemplazada en el input file');
                        });
                    });
                    if (!nextButton.classList.contains('editar')) {
                        nextButton.classList.add('hide');
                        document.getElementById('post_form_compartir').classList.remove('hide');
                        document.getElementById('filtros').classList.add('hide');
                        bodyForm.classList.remove('hide');
                        descriptionInput.classList.remove('hide')
                        document.getElementById('textoInput').textContent = "Descripcion";
                        imagePost.classList.add('hide');
                        filterDiv.appendChild(bodyForm);
                    }
                    if (filtros) {
                        Array.from(filtros).forEach(filtro => {
                            filtro.addEventListener('click', (ev) => {
                                switch (filtro.getAttribute('data-filters')) {
                                    case 'blancoNegro':
                                        imageDiv.firstElementChild.style.filter = 'grayscale(100%)';
                                        filtrosAplicados = imageDiv.firstElementChild.style.filter;
                                        break;
                                    case 'desenfoque':
                                        imageDiv.firstElementChild.style.filter = 'blur(2px)';
                                        filtrosAplicados = imageDiv.firstElementChild.style.filter;
                                        break;
                                    case 'sepia':
                                        imageDiv.firstElementChild.style.filter = 'sepia(100%)';
                                        filtrosAplicados = imageDiv.firstElementChild.style.filter;
                                        break;
                                    case 'invertir':
                                        imageDiv.firstElementChild.style.filter = 'invert(100%)';
                                        filtrosAplicados = imageDiv.firstElementChild.style.filter;
                                        break;
                                    case 'normal':
                                        imageDiv.firstElementChild.style.filter = 'none';
                                        filtrosAplicados = imageDiv.firstElementChild.style.filter;
                                        break;
                                }
                            })

                            function actualizarFiltros() {
                                // Aplica todos los filtros acumulados
                                imageDiv.firstElementChild.style.filter = `saturate(${saturacion}%) contrast(${contraste}%)`;
                                filtrosAplicados = imageDiv.firstElementChild.style.filter;
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
                    }
                });
            }
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

                if (filtrosAplicados) {
                    // Aplicamos los filtros acumulados
                    ctx.filter = filtrosAplicados;
                }

                // Redibujamos la imagen con los filtros aplicados
                ctx.drawImage(img, 0, 0, img.width, img.height);

                // Convertimos el canvas a un Blob (imagen procesada)
                return new Promise((resolve) => {
                    canvas.toBlob((blob) => {
                        resolve(blob);
                    });
                });
            }

            // Función para reemplazar el archivo en el input con la imagen procesada
            async function reemplazarArchivoConImagenProcesada() {
                const blob = await obtenerImagenProcesada();

                // Crear un nuevo archivo de tipo imagen
                const processedFile = new File([blob], 'imagenProcesada.jpg', {type: 'image/jpg'});

                // Crear un nuevo FileList con el archivo procesado
                const dataTransfer = new DataTransfer();
                dataTransfer.items.add(processedFile);

                // Reemplazar el archivo en el input
                imagePost.files = dataTransfer.files;
                console.log(filtrosAplicados)
                // Log para verificar que el archivo ha sido reemplazado correctamente
                console.log(imagePost.files[0]);
            }

// Llamar a esta función cuando se quiera reemplazar la imagen en el input
        });

        // Mostrar el botón de compartir
        $('#compartir').removeClass('hide');

        $('#formPost').on('submit', 'form', function (e) {
            e.preventDefault();

            $('#formPost button[type="submit"]').prop('disabled', true);
            const formData = new FormData(this); // Crea un FormData con el formulario

            $.ajax({
                url: '/post/new', // La ruta configurada en el formulario
                type: 'POST', // El método (debería ser POST)
                data: formData,
                processData: false,
                contentType: false,
                success: function (response) {
                    if (response.status === 'success') {
                        console.log(response.message)// Muestra un mensaje de éxito
                        $('#formPost').addClass('hide');// Oculta el formulario
                        document.body.style.overflow = 'scroll';
                        const newDeleteButton = $('#postSection').find('button.deleteImage');

                        $(newDeleteButton).click((ev) => {
                            ev.preventDefault();
                            const id = $(ev.currentTarget).data('id'); // Accede al data-id correctamente

                            // Muestra un botón o confirma la eliminación
                            $(`a[data-id=${id}]`).toggleClass('hide').click((ev) => {
                                ev.preventDefault();

                                $.get(`/post/delete/${id}`, (response) => {
                                    if (response.success) {
                                        // Si la eliminación es exitosa, elimina el post del DOM
                                        $(`#post-${id}`).slideUp(400, function () {
                                            $(this).remove(); // Elimina el elemento una vez completada la animación
                                        });
                                    }
                                });
                            });
                        });
                    }

                    window.location.reload()
                },
                error: function () {
                    alert('Hubo un error al enviar el formulario');
                }
            });
        });
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


    $('#postSection').on('input','.comment-input', (e) => {
        const input = e.currentTarget;
        const parent = $(input).parent();
        const postComment = $(parent).parent();

        const publish = $(postComment).find('.publish');

        if (input.value) {
            console.log('hOla')
            publish[0].removeAttribute('disabled');
            console.log(publish[0])
        } else {
            publish[0].setAttribute('disabled', 'true');
        }

    })
    $('#postSection').on('click', '.emoji', (e) => {
        e.preventDefault();
        const emojiButton = e.currentTarget;
        const rect = emojiButton.getBoundingClientRect();
        const scrollTop = window.scrollY || document.documentElement.scrollTop;

        // Posiciona el modal cerca del botón de emoji
        emojiModal.style.top = `${rect.top + scrollTop - emojiModal.offsetHeight - 100}px`;
        emojiModal.style.left = `${rect.left}px`;

        // Alterna la visibilidad del modal
        emojiModal.classList.toggle("hide");

        e.stopPropagation();

        // Encuentra el input relacionado al emoji clickeado
        const parent = $(emojiButton).parent();
        const input = parent.find('.comment-input');

        // Reasigna eventos a los emojis en el modal
        emojis.off('click');
        emojis.on('click', (e) => {
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
        console.log(e.target)
        if (!emojiModal.contains(e.target) && !e.target.classList.contains("emoji") && !commentModal.contains(e.target) && !e.target.classList.contains('formulario-post') && !document.getElementById('formPost').contains(e.target) && e.target.id !== 'create-post') {
            emojiModal.classList.add("hide");
            commentModal.classList.add('hide');
            document.body.style.overflow = "auto";
            document.getElementById('formPost').classList.add('hide');
        }
    });

    publicaciones_title = document.getElementById("publicaciones-title");
    guardados = document.getElementById("guardados");

    posts = document.getElementById("publicaciones");
    postGuardados = document.getElementById("publicaciones-guardadas");


    publicaciones_title.addEventListener("click", function () {
        postGuardados.style.display = "none";
        posts.style.display = "grid";
        publicaciones_title.classList.add("active");
        guardados.classList.remove("active");
    })


    guardados.addEventListener("click", function () {
        posts.style.display = "none";
        postGuardados.style.display = "grid";
        publicaciones_title.classList.remove("active");
        guardados.classList.add("active");
    })

    const unfollowed = document.getElementById("unfollowed");
    if (unfollowed) {
        unfollowed.addEventListener("click", function () {
            XHR = new XMLHttpRequest();
            const id = document.getElementById("profile-name").getAttribute("data-id");
            XHR.open("POST", `/addFollowing/${id}`);
            XHR.addEventListener("readystatechange", function () {
                if (XHR.readyState !== 4) {
                    return;
                }
                if (XHR.status === 200) {
                    unfollowed.classList.add("hide");
                    document.getElementById("followed").classList.remove("hide");
                    const jsonFolloewers = JSON.parse(XHR.responseText);
                    $('#followers').text(jsonFolloewers.followers + " Followers");
                }
            })
            XHR.send();
        })
    }

    const followed = document.getElementById("followed");
    if (followed) {
        followed.addEventListener("click", function () {
            XHR = new XMLHttpRequest();
            const id = document.getElementById("profile-name").getAttribute("data-id");
            XHR.open("POST", `/removeFollowing/${id}`);
            XHR.addEventListener("readystatechange", function () {
                if (XHR.readyState !== 4) {
                    return;
                }
                if (XHR.status === 200) {
                    followed.classList.add("hide");
                    document.getElementById("unfollowed").classList.remove("hide");
                    const jsonFolloewers = JSON.parse(XHR.responseText);
                    $('#followers').text(jsonFolloewers.followers + " Followers");
                }
            })
            XHR.send();
        })
    }
}

