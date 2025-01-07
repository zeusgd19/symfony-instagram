window.onload = () => {
    const emojiModal = document.getElementById("modalEmoji");
    const comments = $(".comment");
    const emojis = $('#modalEmoji').find('p');
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

     function initModal(type) {
        const isPost = type === 'post';
        const apiEndpoint = isPost ? "/post/new" : "/story/new";
        const formId = isPost ? '#formPost' : '#formStory';
        const photoForm = isPost ? 'post_form_photo' : 'story_form_photo';
        const compartirButtonId = isPost ? 'post_form_compartir' : 'story_form_compartir';
            document.body.style.overflow = 'hidden';
            // Cargar formulario dinámicamente
             $.ajax({
                type: 'POST',
                url: apiEndpoint,
                dataType: 'html',
                    beforeSend: function() {
                        $(formId).slideDown();

                    },
                    success: function(data) {
                        $(formId).html(data);
                        const nextButton = document.getElementById('nextButton');
                        const bodyForm = document.getElementById('body-form');
                        const filterDiv = document.getElementById('filters');
                        const imageDiv = document.getElementById('imageDiv');
                        const imageInput = document.getElementById(photoForm);
                        const descriptionInput = document.getElementById('post_form_description'); // Solo para posts
                        const compartirButton = compartirButtonId ? document.getElementById(compartirButtonId) : null;
                        let saturacion = 100, contraste = 100, filtrosAplicados = '';
                        if (descriptionInput || filterDiv) {
                            compartirButton.classList.toggle('hide', false);
                        }

                        // Actualizar filtros
                        const actualizarFiltros = () => {
                            imageDiv.firstElementChild.style.filter = `saturate(${saturacion}%) contrast(${contraste}%)`;
                            filtrosAplicados = imageDiv.firstElementChild.style.filter;
                        };

                        // Procesar imagen con filtros aplicados
                        const obtenerImagenProcesada = () => {
                            const canvas = document.createElement('canvas');
                            const ctx = canvas.getContext('2d');
                            const img = imageDiv.firstElementChild;
                            if(!img.getAttribute('data-type')) {
                                canvas.width = img.width;
                                canvas.height = img.height;
                                ctx.filter = filtrosAplicados;
                                ctx.drawImage(img, 0, 0, img.width, img.height);
                                return new Promise((resolve) => {
                                    canvas.toBlob((blob) => resolve(blob));
                                });
                            }
                        };

                        const reemplazarArchivoConImagenProcesada = async () => {
                            const blob = await obtenerImagenProcesada();
                            const processedFile = new File([blob], 'imagenProcesada.jpg', { type: 'image/jpg' });
                            const dataTransfer = new DataTransfer();
                            dataTransfer.items.add(processedFile);
                            imageInput.files = dataTransfer.files;
                        };

                        // Manejar subida de imagen
                        if (imageInput) {
                            imageInput.addEventListener('change', (ev) => {
                                const file = ev.target.files[0];
                                if (!file) return;

                                bodyForm.classList.add('hide');
                                const img = document.createElement('img');
                                if(file.type == 'image/gif') {
                                    img.setAttribute('data-type','gif');
                                }
                                img.src = URL.createObjectURL(file);
                                img.classList.add('imagenDiv');
                                img.onload = () => (img.width = img.height = 400);

                                imageDiv.appendChild(img);
                                nextButton.classList.remove('hide');
                            });
                        }

                        // Manejar botón "Siguiente/Editar"
                        if (nextButton) {
                            nextButton.addEventListener('click', () => {
                                nextButton.classList.toggle('editar');
                                if (nextButton.classList.contains('editar')) {
                                    bodyForm.classList.add('hide');
                                    filterDiv.classList.remove('hide');
                                    filterDiv.prepend(imageDiv);
                                    document.getElementById('tituloCabecera').textContent = 'Editar';
                                } else {
                                    if(!isPost) {
                                        nextButton.classList.add('hide');
                                        filterDiv.classList.add('hide');
                                        bodyForm.classList.remove('hide');
                                        $(bodyForm).find('p').remove();
                                        $(imageInput).parent().hide();
                                        $(bodyForm).append(imageDiv);
                                        compartirButton.classList.remove('hide');
                                    } else {
                                        nextButton.classList.add('hide');
                                        filterDiv.classList.add('hide');
                                        bodyForm.classList.remove('hide');
                                        descriptionInput.classList.remove('hide');
                                        imageInput.classList.add('hide');
                                    }
                                    if(imageDiv.firstElementChild.getAttribute('data-type') != 'gif') {
                                        reemplazarArchivoConImagenProcesada();
                                    }
                                }

                                // Filtros
                                Array.from(document.getElementById('filtros').children).forEach(filtro => {
                                    filtro.addEventListener('click', () => {
                                        const filterType = filtro.getAttribute('data-filters');
                                        const filterMap = {
                                            blancoNegro: 'grayscale(100%)',
                                            desenfoque: 'blur(2px)',
                                            sepia: 'sepia(100%)',
                                            invertir: 'invert(100%)',
                                            normal: 'none'
                                        };
                                        imageDiv.firstElementChild.style.filter = filterMap[filterType] || 'none';
                                        filtrosAplicados = imageDiv.firstElementChild.style.filter;
                                    });
                                });

                                // Saturación y Contraste
                                document.getElementById("saturacion").addEventListener("input", (ev) => {
                                    saturacion = ev.target.value;
                                    actualizarFiltros();
                                });
                                document.getElementById("contraste").addEventListener("input", (ev) => {
                                    contraste = ev.target.value;
                                    actualizarFiltros();
                                });
                            });
                        }
                    }
            })
        // Enviar formulario
        $(formId).off('submit').on('submit', 'form', function (e) {
            e.preventDefault();
            const formData = new FormData(this);
            console.log(formData)
            $.ajax({
                url: apiEndpoint,
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function (response) {
                    if (response.status === 'success') {
                        $(formId).addClass('hide');
                        document.body.style.overflow = 'scroll';
                        window.location.reload();
                    }
                },
                error: () => alert('Hubo un error al enviar el formulario')
            });

        });
    }

    $('#create-post').click(function(e){
        e.preventDefault();
        console.log('Hola')
        initModal('post');
    })

    $('#create-story').click(function(e){
        e.preventDefault();
        initModal('story');
    })



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

    $(document).on('click','.publish', function(ev) {
        ev.preventDefault();  // Prevenir el comportamiento por defecto (recarga de la página)
        let postId = $(this).parent().attr('data-id');
        let value = $(this).parent().find('input').val();
        let input = $(this).parent().find('input');
        console.log('Hola')
        $.ajax({
            type: "POST",
            url: "/comment/new",
            data: JSON.stringify({ postId: postId, comment: value }),
            contentType: "application/json",
            success: function (response) {
                let hasComments = $('.comments-box').find('.comment-item-whithout-comment') ? true : false;

                if(hasComments) $('.comments-box').find('.comment-item-whithout-comment').remove();
                $('.comments-box').append(
                    `<div class="comment-item">
                            <div>
                                <img src="${response.commentUserPhoto}" alt="user-image" loading="lazy">
                                <p class="username">${response.commentUserUsername}</p>
                                <p class="comment-text">${response.comment}</p>
                            </div>
                            <img src="img/post-like.svg" alt="like-icon" loading="lazy">
                        </div>
                    `
                )
                $(input).val("");
            }
        });
    });

    $(document).on('click','.story', function(e){
        e.preventDefault();
        const userId = $(this).attr('data-id');
        window.location.href = `/story/${userId}`;
    })

    $('#postSection').on('click', '.emoji', (e) => {
        e.preventDefault();
        const emojiButton = e.currentTarget;
        const rect = emojiButton.getBoundingClientRect();
        const scrollTop = window.scrollY || document.documentElement.scrollTop;

        emojiModal.style.top = `${rect.top + scrollTop - emojiModal.offsetHeight - 100}px`;
        emojiModal.style.left = `${rect.left}px`;


        emojiModal.classList.toggle("hide");

        e.stopPropagation();


        const parent = $(emojiButton).parent();
        const input = parent.find('.comment-input');


        emojis.off('click');
        emojis.on('click', (e) => {
            e.preventDefault();
            input.val(input.val() + $(e.target).text());
        });
        $(document).on('click', function (event) {
            if (!$(event.target).closest(emojiModal).length && !$(event.target).is($(emojis))) {
                $(emojiModal).addClass('hide');
                document.body.style.overflow = "";
                $(document).off('click');
            }
        });
    });

    $(comments).off().on('click', function (ev) {
        ev.preventDefault();
        console.log('Hola');
        const postId = $(this).attr('id');
        $.ajax({
            type: 'POST',
            url: `/comments/${postId}`,
            dataType: 'html',
            beforeSend: function() {
                $('#modalComment').html('<div class="loading-container"><img src="img/loading-buffer.gif" width="30" height="30"></div>');

                $('#modalComment').slideDown();
            },
            success: function(data) {
                $('#modalComment').html(data);
            }
        })
    });

    $(document).on('click', function (event) {
        const activeModals = ['#commentModal', '#modalEmoji', '#formPost', '#formStory']; // Selección de todos los modales
        const openButtonSelectors = ['.emoji', '.formulario-post', '.comment', '#create-story', '#create-post']; // Botones que abren las modales

        activeModals.forEach(modalSelector => {
            const modal = $(modalSelector);


            if (!modal.is(event.target) && modal.has(event.target).length === 0 && !$(event.target).closest(openButtonSelectors.join(', ')).length) {
                console.log('Clic fuera del modal');
                modal.hide('slow'); // Ocultar modal
                document.body.style.overflow = ""; // Restaurar scroll
            }
        });
    });

    $(document).on('click', '.unlikedPost, .likedPost', function (ev) {
        ev.preventDefault();
        let postId = $(this).attr('id');
        const isLiked = $(this).hasClass('likedPost');


        const url = isLiked ? `/removeLike/${postId}` : `/addLike/${postId}`;

        const xhr = new XMLHttpRequest();
        xhr.open('POST', url);

        xhr.addEventListener('readystatechange', (ev) => {
            if (xhr.readyState !== 4) return;

            if (xhr.status >= 200 && xhr.status < 300) {
                let json = JSON.parse(xhr.responseText);


                $(`#post-${postId}`).find('.likesByOthers').text(json.totalLikes + " people");
                let likedByYou = json.likedBYYou;
                let othersLikes = json.totalLikes - (likedByYou ? 1 : 0);


                let likeMessage = "";
                if (likedByYou) {
                    $(`#post-${postId}`).find('.imgLike').attr('src', 'img/post-like-liked.svg');
                    $('#commentModal').find('.imgLike').attr('src', 'img/post-like-liked.svg');
                    if (othersLikes > 0) {
                        likeMessage = "Liked by " + "<span>you</span> " + `and <span>${othersLikes} others</span>`;
                    } else {
                        likeMessage = "Liked by <span>you</span>";
                    }
                    $(this).removeClass('unlikedPost').addClass('likedPost');
                } else {
                    $(`#post-${postId}`).find('.imgLike').attr('src', 'img/post-like.svg');
                    $('#commentModal').find('.imgLike').attr('src', 'img/post-like.svg');
                    likeMessage = `Liked by <span>${json.totalLikes} people</span>`;
                    $(this).removeClass('likedPost').addClass('unlikedPost');
                }


                $(`#post-${postId}`).find('.likeMessage').html(likeMessage);
                $(`#commentModal`).find('.likeMessage').html(likeMessage);
            }
        });

        xhr.send();
    });

    $(document).on('click', '.unsavedPost, .savedPost', function (ev) {
        ev.preventDefault();
        let postId = $(this).attr('id');
        const isSaved = $(this).hasClass('savedPost');


        const url = isSaved ? `/removeSave/${postId}` : `/addSave/${postId}`;

        const xhr = new XMLHttpRequest();
        xhr.open('POST', url);

        xhr.addEventListener('readystatechange', (ev) => {
            if (xhr.readyState !== 4) return;

            if (xhr.status >= 200 && xhr.status < 300) {
                let json = JSON.parse(xhr.responseText);

                let savedByYou = json.savedBYYou;

                console.log(savedByYou);

                if (savedByYou) {
                    $(`#post-${postId}`).find('.imgSave').attr('src', 'img/post-save-saved.svg');
                    $('#commentModal').find('.imgSave').attr('src', 'img/post-save-saved.svg');

                    $(this).removeClass('unsavedPost').addClass('savedPost');
                } else {
                    $(`#post-${postId}`).find('.imgSave').attr('src', 'img/post-save.svg');
                    $('#commentModal').find('.imgSave').attr('src', 'img/post-save.svg');
                    $(this).removeClass('savedPost').addClass('unsavedPost');
                }
            }
        });

        xhr.send();
    });

    $(document).on('click','.follow-btn, .unfollow-btn',function(ev) {
        ev.preventDefault();
        const id = ev.target.parentElement.getAttribute('data-id');
        const isFollowed = $(this).hasClass('followed');
        const url = isFollowed ? `/removeFollowing/${id}` : `/addFollowing/${id}`;

        const xhr = new XMLHttpRequest();
        xhr.open('POST', url);
        xhr.addEventListener('readystatechange', (ev) => {
            if (xhr.readyState !== 4) return;

            if (xhr.status >= 200 && xhr.status < 300) {

                if (isFollowed) {
                    $(this).text('Follow');
                    $(this).removeClass('followed').addClass('unfollowed');
                } else {
                    $(this).text('Following');
                    $(this).removeClass('unfollowed').addClass('followed');
                }
            }
        });
        xhr.send();
    })


    /*
    postGuardados = document.getElementById("publicaciones-guardadas");
    publicaciones_title = document.getElementById('publicaciones-title');
    guardados = document.getElementById('guardados');
    posts = document.getElementById('publicaciones')
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
     */
}

