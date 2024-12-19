$(document).ready(function () {
    let stories;
    let currentIndex = parseInt($('.story-main-content').attr('data-index'));
    let userId = $('.story-main-content').attr('data-id');

    const isMobile = () => window.innerWidth <= 600;

    $.ajax({
        type: 'GET',
        url: '/stories',
        contentType: 'application/json',
        success: function (data) {
            stories = data.stories;

            const currentStory = stories[currentIndex];
            userId = currentStory.userId;
            updateStory('.story-center', currentStory.image, currentStory.userPhoto, currentStory.userUsername);
            updateNeighbors();
        }
    });

    function* generateStoryLines(stories, userId) {
        for (const story of stories) {
            if (story.userId == userId) {
                yield '<hr>';
            }
        }
    }


    $(document).on('click', '.next, .previous', function (e) {
        e.preventDefault();

        if (!stories || stories.length <= 1) return;

        const isNext = $(this).hasClass('next');
        const increment = isNext ? 1 : -1;
        let newIndex = currentIndex + increment;

        if (newIndex >= 0 && newIndex < stories.length) {
            if (stories[newIndex].userId == userId) {
                currentIndex = newIndex;
            } else {
                userId = stories[newIndex].userId;
                let value = '';
                for (const line of generateStoryLines(stories, userId)) {
                    value += line;
                }
                $('.storiesLine').html(value);
                window.history.pushState({}, '', '/story/' + userId);
                currentIndex = stories.findIndex((element) => element.userId == userId);
            }

            console.log(currentIndex);
            const currentStory = stories[currentIndex];
            updateStory('.story-center', currentStory.image, currentStory.userPhoto, currentStory.userUsername);
        }

        updateNeighbors();
    });

    window.addEventListener('resize',function(){
        if(isMobile()){
            $(".story-left").hide();
            $(".story-right").hide();
        } else {
            updateNeighbors();
        }
    })

    function updateNeighbors() {
        let prevIndex = findNeighborPreviousIndex(currentIndex, -1);
        if (isMobile()) {
            $(".story-left").hide();
            $(".story-right").hide();
            $('.previous').toggle(currentIndex > 0);
            $('.next').toggle(currentIndex < stories.length - 1);
        } else {
            if (prevIndex !== null) {
                const prevStory = stories[prevIndex];
                updateStory('.story-left', prevStory.image, prevStory.userPhoto, prevStory.userUsername);
                $('.story-left, .previous').show();
            } else {
                if (currentIndex == 0) {
                    $('.story-left, .previous').hide();
                } else {
                    $('.story-left').hide();
                    $('.previous').show();
                }
            }

            let nextIndex = findNeighborNextIndex(currentIndex, 1);
            if (nextIndex !== null) {
                const nextStory = stories[nextIndex];
                updateStory('.story-right', nextStory.image, nextStory.userPhoto, nextStory.userUsername);
                $('.story-right, .next').show();
            } else {
                if (currentIndex == stories.length - 1) {
                    $('.story-right, .next').hide();
                } else {
                    $('.story-right').hide();
                    $('.next').show();
                }
            }
        }
    }

    function findNeighborNextIndex(startIndex, direction) {
        const generator = findNeighbors(startIndex, direction, stories, userId);
        const nextStory = generator.next().value;
        if (nextStory) {
            return stories.findIndex((element) => element.userId == nextStory.userId);
        }
        return null;
    }

    function findNeighborPreviousIndex(startIndex, direction) {
        const generator = findNeighbors(startIndex, direction, stories, userId);
        const prevStory = generator.next().value;
        if (prevStory) {
            return stories.findIndex((element) => element.userId == prevStory.userId);
        }
        return null;
    }


    function* findNeighbors(startIndex, direction, stories, userId) {
        let i = startIndex + direction;
        while (i >= 0 && i < stories.length) {
            if (stories[i].userId != userId) {
                yield stories[i]; // Pausa y devuelve la historia actual.
            }
            i += direction;
        }
    }


    function updateStory(selector, storyImage, photo, username) {
        const container = $(selector);
        container.find('.story img').attr('src', storyImage);
        container.find('.story-name-photo img').attr('src', photo);
        container.find('.story-name-photo p').text(username);
    }
});
