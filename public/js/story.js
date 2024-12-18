$(document).ready(function(){
    let stories;
    const userId = $('.story-main-content').attr('data-id');
    let currentIndex = $('.story-main-content').attr('data-index');
    console.log(currentIndex);

    $.ajax({
        type: 'GET',
        url: '/stories',
        contentType: 'application/json',
        success: function(data) {
            stories = data.stories;
        }
    });

    $(document).on('click', '.next, .previous', function (e) {
        e.preventDefault();

        if (stories.length <= 1) return;

        const isNext = $(this).hasClass('next');
        const increment = isNext ? 1 : -1;

        currentIndex = parseInt(currentIndex) + increment;
        console.log(currentIndex)

        const { image: currentStory, userPhoto: currentPhoto, userUsername: currentUsername } = stories[currentIndex];
        updateStory('.story-center', currentStory, currentPhoto, currentUsername);

        console.log(currentIndex);
        $('.next').toggle(currentIndex < stories.length - 1);
        $('.story-right').toggle(currentIndex < stories.length - 1);

        $('.previous').toggle(currentIndex > 0);
        $('.story-left').toggle(currentIndex > 0);

        if (currentIndex > 0) {
            const { image: lastStory, userPhoto: lastPhoto, userUsername: lastUsername } = stories[currentIndex - 1];
            updateStory('.story-left', lastStory, lastPhoto, lastUsername);
        }

        if (currentIndex < stories.length - 1) {
            const { image: nextStory, userPhoto: nextPhoto, userUsername: nextUsername } = stories[currentIndex + 1];
            updateStory('.story-right', nextStory, nextPhoto, nextUsername);
        }
    });

    function updateStory(selector, story, photo, username) {
        const container = $(selector);
        container.find('.story').find('img').attr('src', story);
        container.find('.story-name-photo').find('img').attr('src', photo);
        container.find('.story-name-photo').find('p').text(username);
    }

})