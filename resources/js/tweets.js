function handleInteraction(form, successCallback) {
    form.addEventListener("submit", function (e) {
        e.preventDefault();

        // Get the tweet container and check opposite action
        const tweetContainer = form.closest(".tweet");
        const isLikeForm = form.classList.contains("like-form");
        const oppositeForm = tweetContainer?.querySelector(
            isLikeForm ? ".dislike-form" : ".like-form"
        );

        fetch(form.action, {
            method: "POST",
            headers: {
                "X-CSRF-TOKEN": document.querySelector(
                    'meta[name="csrf-token"]'
                ).content,
                Accept: "application/json",
            },
        })
            .then((response) => {
                if (!response.ok) {
                    throw new Error("Network response was not ok");
                }
                return response.json();
            })
            .then((data) => {
                if (data.success) {
                    // Handle opposite form if this is a new like/dislike
                    if ((data.liked || data.disliked) && oppositeForm) {
                        const oppositeButton =
                            oppositeForm.querySelector("button");
                        const oppositeCount = oppositeForm.querySelector(
                            isLikeForm ? ".dislike-count" : ".like-count"
                        );

                        // Update opposite form counts and state
                        if (oppositeButton && oppositeCount) {
                            if (isLikeForm && data.wasDisliked) {
                                oppositeButton.innerHTML = `<svg viewBox="0 0 20 20" class="w-5 mt-2 mr-1 fill-gray-400" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M11.0010436,20 C9.89589787,20 9.00000024,19.1132936 9.0000002,18.0018986 L9,12 L1.9973917,12 C0.894262725,12 0,11.1122704 0,10 L0,8 L2.29663334,1.87564456 C2.68509206,0.839754676 3.90195042,8.52651283e-14 5.00853025,8.52651283e-14 L12.9914698,8.52651283e-14 C14.1007504,8.52651283e-14 15,0.88743329 15,1.99961498 L15,10 L12,17 L12,20 L11.0010436,20 L11.0010436,20 Z M17,10 L20,10 L20,0 L17,0 L17,10 L17,10 Z"></path>
                                </svg>`;
                                oppositeCount.textContent -= 1;
                            } else if (!isLikeForm && data.wasLiked) {
                                oppositeButton.innerHTML = `<svg viewBox="0 0 24 24" aria-hidden="true" class="w-6">
                                    <g class="fill-current">
                                        <path d="M16.697 5.5c-1.222-.06-2.679.51-3.89 2.16l-.805 1.09-.806-1.09C9.984 6.01 8.526 5.44 7.304 5.5c-1.243.07-2.349.78-2.91 1.91-.552 1.12-.633 2.78.479 4.82 1.074 1.97 3.257 4.27 7.129 6.61 3.87-2.34 6.052-4.64 7.126-6.61 1.111-2.04 1.03-3.7.477-4.82-.561-1.13-1.666-1.84-2.908-1.91zm4.187 7.69c-1.351 2.48-4.001 5.12-8.379 7.67l-.503.3-.504-.3c-4.379-2.55-7.029-5.19-8.382-7.67-1.36-2.5-1.41-4.86-.514-6.67.887-1.79 2.647-2.91 4.601-3.01 1.651-.09 3.368.56 4.798 2.01 1.429-1.45 3.146-2.1 4.796-2.01 1.954.1 3.714 1.22 4.601 3.01.896 1.81.846 4.17-.514 6.67z"></path>
                                    </g>
                                </svg>`;
                                oppositeCount.textContent -= 1;
                            }
                        }
                    }
                    successCallback(data);
                }
            })
            .catch((error) => console.error("Error:", error));
    });
}

document.addEventListener("DOMContentLoaded", function () {
    const likeForms = document.querySelectorAll(".like-form");
    const dislikeForms = document.querySelectorAll(".dislike-form");
    const shareForms = document.querySelectorAll(".share-form");

    likeForms.forEach((form) => {
        handleInteraction(form, function (data) {
            const likeButton = form.querySelector("button");
            const likeCount = form.querySelector(".like-count");

            if (likeButton && likeCount) {
                likeButton.innerHTML = data.liked
                    ? `<svg xmlns="http://www.w3.org/2000/svg" class="w-5" viewBox="0 0 36 36">
                        <path fill="red" d="M35.885 11.833c0-5.45-4.418-9.868-9.867-9.868-3.308 0-6.227 1.633-8.018 4.129-1.791-2.496-4.71-4.129-8.017-4.129-5.45 0-9.868 4.417-9.868 9.868 0 .772.098 1.52.266 2.241C1.751 22.587 11.216 31.568 18 34.034c6.783-2.466 16.249-11.447 17.617-19.959.17-.721.268-1.469.268-2.242z"/>
                    </svg>`
                    : `<svg viewBox="0 0 24 24" aria-hidden="true" class="w-6">
                        <g class="fill-current">
                            <path d="M16.697 5.5c-1.222-.06-2.679.51-3.89 2.16l-.805 1.09-.806-1.09C9.984 6.01 8.526 5.44 7.304 5.5c-1.243.07-2.349.78-2.91 1.91-.552 1.12-.633 2.78.479 4.82 1.074 1.97 3.257 4.27 7.129 6.61 3.87-2.34 6.052-4.64 7.126-6.61 1.111-2.04 1.03-3.7.477-4.82-.561-1.13-1.666-1.84-2.908-1.91zm4.187 7.69c-1.351 2.48-4.001 5.12-8.379 7.67l-.503.3-.504-.3c-4.379-2.55-7.029-5.19-8.382-7.67-1.36-2.5-1.41-4.86-.514-6.67.887-1.79 2.647-2.91 4.601-3.01 1.651-.09 3.368.56 4.798 2.01 1.429-1.45 3.146-2.1 4.796-2.01 1.954.1 3.714 1.22 4.601 3.01.896 1.81.846 4.17-.514 6.67z"></path>
                        </g>
                    </svg>`;

                likeCount.textContent = data.likesCount;
            }
        });
    });

    dislikeForms.forEach((form) => {
        handleInteraction(form, function (data) {
            const dislikeButton = form.querySelector("button");
            const dislikeCount = form.querySelector(".dislike-count");

            if (dislikeButton && dislikeCount) {
                dislikeButton.innerHTML = data.disliked
                    ? `<svg viewBox="0 0 20 20" class="w-5 mt-2 mr-1 fill-black" xmlns="http://www.w3.org/2000/svg">
                        <path d="M11.0010436,20 C9.89589787,20 9.00000024,19.1132936 9.0000002,18.0018986 L9,12 L1.9973917,12 C0.894262725,12 0,11.1122704 0,10 L0,8 L2.29663334,1.87564456 C2.68509206,0.839754676 3.90195042,8.52651283e-14 5.00853025,8.52651283e-14 L12.9914698,8.52651283e-14 C14.1007504,8.52651283e-14 15,0.88743329 15,1.99961498 L15,10 L12,17 L12,20 L11.0010436,20 L11.0010436,20 Z M17,10 L20,10 L20,0 L17,0 L17,10 L17,10 Z"></path>
                    </svg>`
                    : `<svg viewBox="0 0 20 20" class="w-5 mt-2 mr-1 fill-gray-400" xmlns="http://www.w3.org/2000/svg">
                        <path d="M11.0010436,20 C9.89589787,20 9.00000024,19.1132936 9.0000002,18.0018986 L9,12 L1.9973917,12 C0.894262725,12 0,11.1122704 0,10 L0,8 L2.29663334,1.87564456 C2.68509206,0.839754676 3.90195042,8.52651283e-14 5.00853025,8.52651283e-14 L12.9914698,8.52651283e-14 C14.1007504,8.52651283e-14 15,0.88743329 15,1.99961498 L15,10 L12,17 L12,20 L11.0010436,20 L11.0010436,20 Z M17,10 L20,10 L20,0 L17,0 L17,10 L17,10 Z"></path>
                    </svg>`;

                dislikeCount.textContent = data.dislikesCount;
            }
        });
    });

    shareForms.forEach((form) => {
        handleInteraction(form, function (data) {
            const shareButton = form.querySelector("button");
            const shareCount = form.querySelector(".share-count");

            if (shareButton && shareCount) {
                shareButton.innerHTML = data.shared
                    ? `<svg class="" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="green" fill="none" stroke-linecap="round" stroke-linejoin="round" data-testid="svg-icon">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                        <path d="M4 12v-3a3 3 0 0 1 3 -3h13m-3 -3l3 3l-3 3"></path>
                        <path d="M20 12v3a3 3 0 0 1 -3 3h-13m3 3l-3 -3l3 -3"></path>
                    </svg>`
                    : `<svg class="" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="black" fill="none" stroke-linecap="round" stroke-linejoin="round" data-testid="svg-icon">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                        <path d="M4 12v-3a3 3 0 0 1 3 -3h13m-3 -3l3 3l-3 3"></path>
                        <path d="M20 12v3a3 3 0 0 1 -3 3h-13m3 3l-3 -3l3 -3"></path>
                    </svg>`;

                shareCount.textContent = data.sharesCount;

                if (data.shared) {
                    const successMessage = document.createElement("div");
                    successMessage.className =
                        "fixed bottom-4 right-4 bg-green-500 text-white px-4 py-2 rounded-lg shadow-lg";
                    successMessage.textContent = "Tweet shared successfully!";
                    document.body.appendChild(successMessage);

                    setTimeout(() => {
                        successMessage.remove();
                    }, 3000);
                }
            }
        });
    });
});
