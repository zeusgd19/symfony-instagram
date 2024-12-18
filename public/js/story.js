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

    $(document).on('click', '.next, .previous', function (e) {
        e.preventDefault();

        if (!stories || stories.length <= 1) return;

        const isNext = $(this).hasClass('next');
        const increment = isNext ? 1 : -1;
        let newIndex = currentIndex + increment;

        if (isMobile()) {
            if (newIndex >= 0 && newIndex < stories.length) {
                if (stories[newIndex].userId == userId) {
                    currentIndex = newIndex;
                } else {
                    userId = stories[newIndex].userId;
                    let value = '';
                    stories.forEach((element) => {
                        if (element.userId == userId) {
                            value += "<hr>";
                        }
                    });
                    $('.storiesLine').html(`${value}`);
                    window.history.pushState({}, '', '/story/' + userId);
                    currentIndex = stories.findIndex((element) => element.userId == userId);
                }
                console.log(currentIndex);
                const currentStory = stories[currentIndex];
                updateStory('.story-center', currentStory.image, currentStory.userPhoto, currentStory.userUsername);
            }
        } else {
            if (newIndex >= 0 && newIndex < stories.length) {
                if (stories[newIndex].userId == userId) {
                    currentIndex = newIndex;
                } else {
                    userId = stories[newIndex].userId;
                    let value = '';
                    stories.forEach((element) => {
                        if (element.userId == userId) {
                            value += "<hr>";
                        }
                    });
                    $('.storiesLine').html(`${value}`);
                    window.history.pushState({}, '', '/story/' + userId);
                    currentIndex = stories.findIndex((element) => element.userId == userId);
                }

                console.log(currentIndex);
                const currentStory = stories[currentIndex];
                updateStory('.story-center', currentStory.image, currentStory.userPhoto, currentStory.userUsername);
            }
        }

        updateNeighbors();
    });

    function updateNeighbors() {
        let prevIndex = findNeighborPreviousIndex(currentIndex, -1);
        if (isMobile()) {
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
        let i = startIndex + direction;
        while (i >= 0 && i < stories.length) {
            if (stories[i].userId != userId) {
                let nextUser = stories[i].userId;
                return stories.findIndex((element) => element.userId == nextUser);
            }
            i += direction;
        }
        return null;
    }

    function findNeighborPreviousIndex(startIndex, direction) {
        let i = startIndex + direction;
        while (i >= 0 && i < stories.length) {
            if (stories[i].userId != userId) {
                let previousUser = stories[i].userId;
                return stories.findIndex((element) => element.userId == previousUser);
            }
            i += direction;
        }
        return null;
    }

    function updateStory(selector, storyImage, photo, username) {
        const container = $(selector);
        container.find('.story img').attr('src', storyImage);
        container.find('.story-name-photo img').attr('src', photo);
        container.find('.story-name-photo p').text(username);
    }
});
