document.addEventListener('DOMContentLoaded', function() {
    // Gallery Media Switcher — uses CSS classes (.media-active / .media-hidden)
    // so that display:none !important always wins, even on actively-playing videos.
    const mainImg    = document.getElementById('mainGalleryImg');
    const mainVideo  = document.getElementById('mainGalleryVideo');
    const thumbnails = document.querySelectorAll('.thumb-item');

    function showImage(src) {
        // Stop & hide video
        if (mainVideo) {
            mainVideo.pause();
            mainVideo.currentTime = 0;
            mainVideo.classList.add('media-hidden');
            mainVideo.classList.remove('media-active');
            mainVideo.hidden = true;
            mainVideo.style.display = 'none';
            if (mainVideo.src) {
                mainVideo.removeAttribute('src');
                mainVideo.load();
            }
        }

        // Show image
        if (mainImg) {
            mainImg.src = src;
            mainImg.classList.remove('media-hidden');
            mainImg.classList.add('media-active');
            mainImg.hidden = false;
            mainImg.style.display = 'block';
        }
    }

    function showVideo(src) {
        // Hide image
        if (mainImg) {
            mainImg.classList.add('media-hidden');
            mainImg.classList.remove('media-active');
            mainImg.hidden = true;
            mainImg.style.display = 'none';
        }

        // Show & play video
        if (mainVideo) {
            mainVideo.classList.remove('media-hidden');
            mainVideo.classList.add('media-active');
            mainVideo.hidden = false;
            mainVideo.style.display = 'block';
            if (mainVideo.src !== src) {
                mainVideo.src = src;
            }
            mainVideo.load();
            mainVideo.play().catch(function() {
                // Autoplay blocked by browser policy — video is still visible
            });
        }
    }

    if (thumbnails.length > 0) {
        thumbnails.forEach(function(thumb) {
            thumb.addEventListener('click', function(e) {
                const item = e.currentTarget;
                const src  = item.getAttribute('data-full-src');
                const type = item.getAttribute('data-type') || 'image';

                if (!src) return;

                // Highlight active thumbnail
                thumbnails.forEach(function(t) { t.classList.remove('active'); });
                item.classList.add('active');

                if (type === 'video') {
                    showVideo(src);
                } else {
                    showImage(src);
                }
            });
        });
    }

    // Video Upload Preview in Admin Form
    const videoInput = document.getElementById('vehicleVideosInput');
    const videoPreviewBox = document.getElementById('videoPreviewBox');
    if (videoInput && videoPreviewBox) {
        videoInput.addEventListener('change', function() {
            videoPreviewBox.innerHTML = '';
            if (this.files) {
                Array.from(this.files).forEach(file => {
                    if (file.type.match('video.*')) {
                        const vidDiv = document.createElement('div');
                        vidDiv.className = 'thumb-item video-thumb active';
                        vidDiv.style.width = '100px';
                        vidDiv.style.height = '68px';
                        vidDiv.style.position = 'relative';
                        vidDiv.innerHTML = `<video src="${URL.createObjectURL(file)}#t=0.5" style="width:100%; height:100%; object-fit:cover;" muted></video><span class="thumb-video-icon" style="font-size:0.65rem; padding:2px 5px;"><i class="fa-solid fa-play"></i></span>`;
                        videoPreviewBox.appendChild(vidDiv);
                    }
                });
            }
        });
    }

    // Image Upload Preview in Admin Form
    const fileInput = document.getElementById('vehiclePicsInput');
    const previewBox = document.getElementById('imagePreviewBox');

    if (fileInput && previewBox) {
        fileInput.addEventListener('change', function() {
            previewBox.innerHTML = '';
            if (this.files) {
                Array.from(this.files).forEach(file => {
                    if (file.type.match('image.*')) {
                        const reader = new FileReader();
                        reader.onload = function(e) {
                            const imgDiv = document.createElement('div');
                            imgDiv.className = 'thumb-item active';
                            imgDiv.style.width = '80px';
                            imgDiv.style.height = '60px';
                            imgDiv.innerHTML = `<img src="${e.target.result}" style="width:100%; height:100%; object-fit:cover;">`;
                            previewBox.appendChild(imgDiv);
                        };
                        reader.readAsDataURL(file);
                    }
                });
            }
        });
    }
});
