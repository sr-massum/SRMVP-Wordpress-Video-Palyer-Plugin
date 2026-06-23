/**
 * SRM Video Player — Frontend JS
 * Author: Saieed Rahman | SidMan Solution 2026
 * Copyright: SidMan Solution 2026
 */

(function () {
    'use strict';

    function formatTime(seconds) {
        if (isNaN(seconds) || seconds < 0) return '0:00';
        var m = Math.floor(seconds / 60);
        var s = Math.floor(seconds % 60);
        return m + ':' + (s < 10 ? '0' : '') + s;
    }

    function SRMVPlayer(wrap) {
        this.wrap = wrap;
        this.video = wrap.querySelector('.srmvp-video');
        this.poster = wrap.querySelector('.srmvp-poster:not(.srmvp-iframe-poster)');
        this.controls = wrap.querySelector('.srmvp-controls');

        if (!this.video) {
            this.initIframe();
            return;
        }

        this.playBtn = wrap.querySelector('.srmvp-btn-play');
        this.muteBtn = wrap.querySelector('.srmvp-btn-mute');
        this.volumeSlider = wrap.querySelector('.srmvp-volume-slider');
        this.progressWrap = wrap.querySelector('.srmvp-progress-wrap');
        this.progressBar = wrap.querySelector('.srmvp-progress-bar');
        this.played = wrap.querySelector('.srmvp-progress-played');
        this.buffered = wrap.querySelector('.srmvp-progress-buffered');
        this.thumb = wrap.querySelector('.srmvp-progress-thumb');
        this.currentTimeEl = wrap.querySelector('.srmvp-current-time');
        this.durationEl = wrap.querySelector('.srmvp-duration');
        this.speedBtn = wrap.querySelector('.srmvp-btn-speed');
        this.speedMenu = wrap.querySelector('.srmvp-speed-menu');
        this.speedOpts = wrap.querySelectorAll('.srmvp-speed-opt');
        this.fullscreenBtn = wrap.querySelector('.srmvp-btn-fullscreen');
        this.iconPlay = wrap.querySelector('.srmvp-icon-play');
        this.iconPause = wrap.querySelector('.srmvp-icon-pause');
        this.iconVolume = wrap.querySelector('.srmvp-icon-volume');
        this.iconMuted = wrap.querySelector('.srmvp-icon-muted');
        this.iconFs = wrap.querySelector('.srmvp-icon-fullscreen');
        this.iconExitFs = wrap.querySelector('.srmvp-icon-exit-fullscreen');
        this.centerPlay = wrap.querySelector('.srmvp-center-play');
        this.spinner = wrap.querySelector('.srmvp-spinner');

        this.isSeeking = false;
        this.accentColor = wrap.dataset.accent || '#ff6b35';

        this.bindEvents();
        this.applyAccent();

        var autoplay = wrap.dataset.autoplay === 'true';
        var muted = wrap.dataset.muted === 'true';

        if (muted) {
            this.video.muted = true;
            this.updateMuteIcon(true);
            if (this.volumeSlider) this.volumeSlider.value = 0;
        }

        if (autoplay) {
            this.video.muted = true;
            this.video.play().catch(function () {});
        }

        this.wrap.classList.add('srmvp-paused');
    }

    SRMVPlayer.prototype.applyAccent = function () {
        var acc = this.accentColor;
        if (this.played) this.played.style.background = acc;
        if (this.volumeSlider) {
            this.volumeSlider.style.setProperty('--accent', acc);
        }
    };

    SRMVPlayer.prototype.bindEvents = function () {
        var self = this;
        var video = this.video;

        if (this.poster) {
            this.poster.addEventListener('click', function () {
                self.playToggle();
            });
        }
        if (this.centerPlay && !this.poster) {
            this.centerPlay.addEventListener('click', function () {
                self.playToggle();
            });
        }

        video.addEventListener('click', function () {
            self.playToggle();
        });

        video.addEventListener('play', function () {
            self.onPlay();
        });
        video.addEventListener('pause', function () {
            self.onPause();
        });
        video.addEventListener('ended', function () {
            self.onEnded();
        });
        video.addEventListener('timeupdate', function () {
            self.onTimeUpdate();
        });
        video.addEventListener('progress', function () {
            self.onBufferUpdate();
        });
        video.addEventListener('loadedmetadata', function () {
            if (self.durationEl) {
                self.durationEl.textContent = formatTime(video.duration);
            }
        });
        video.addEventListener('waiting', function () {
            self.wrap.classList.add('srmvp-loading');
        });
        video.addEventListener('canplay', function () {
            self.wrap.classList.remove('srmvp-loading');
        });

        if (this.playBtn) {
            this.playBtn.addEventListener('click', function () {
                self.playToggle();
            });
        }

        if (this.muteBtn) {
            this.muteBtn.addEventListener('click', function () {
                self.muteToggle();
            });
        }

        if (this.volumeSlider) {
            this.volumeSlider.addEventListener('input', function () {
                video.volume = parseFloat(this.value);
                video.muted = parseFloat(this.value) === 0;
                self.updateMuteIcon(video.muted);
            });
        }

        if (this.progressWrap) {
            this.progressWrap.addEventListener('mousedown', function (e) {
                self.isSeeking = true;
                self.seek(e);
            });
            this.progressWrap.addEventListener('mousemove', function (e) {
                if (self.isSeeking) self.seek(e);
            });
            document.addEventListener('mouseup', function () {
                self.isSeeking = false;
            });
            this.progressWrap.addEventListener('touchstart', function (e) {
                self.isSeeking = true;
                self.seek(e.touches[0]);
            }, { passive: true });
            this.progressWrap.addEventListener('touchmove', function (e) {
                if (self.isSeeking) self.seek(e.touches[0]);
            }, { passive: true });
            document.addEventListener('touchend', function () {
                self.isSeeking = false;
            });
        }

        if (this.speedOpts) {
            this.speedOpts.forEach(function (opt) {
                opt.addEventListener('click', function () {
                    var speed = parseFloat(opt.dataset.speed);
                    video.playbackRate = speed;
                    if (self.speedBtn) {
                        self.speedBtn.querySelector('.srmvp-speed-label').textContent = speed + 'x';
                    }
                    self.speedOpts.forEach(function (o) { o.classList.remove('active'); });
                    opt.classList.add('active');
                });
            });
        }

        if (this.fullscreenBtn) {
            this.fullscreenBtn.addEventListener('click', function () {
                self.toggleFullscreen();
            });
        }

        document.addEventListener('fullscreenchange', function () {
            self.onFullscreenChange();
        });
        document.addEventListener('webkitfullscreenchange', function () {
            self.onFullscreenChange();
        });

        document.addEventListener('keydown', function (e) {
            if (!self.wrap.contains(document.activeElement) && document.activeElement !== document.body) return;
            switch (e.code) {
                case 'Space':
                    e.preventDefault();
                    self.playToggle();
                    break;
                case 'ArrowRight':
                    video.currentTime = Math.min(video.currentTime + 5, video.duration || 0);
                    break;
                case 'ArrowLeft':
                    video.currentTime = Math.max(video.currentTime - 5, 0);
                    break;
                case 'ArrowUp':
                    video.volume = Math.min(video.volume + 0.1, 1);
                    if (self.volumeSlider) self.volumeSlider.value = video.volume;
                    break;
                case 'ArrowDown':
                    video.volume = Math.max(video.volume - 0.1, 0);
                    if (self.volumeSlider) self.volumeSlider.value = video.volume;
                    break;
                case 'KeyF':
                    self.toggleFullscreen();
                    break;
                case 'KeyM':
                    self.muteToggle();
                    break;
            }
        });
    };

    SRMVPlayer.prototype.playToggle = function () {
        if (this.video.paused || this.video.ended) {
            this.video.play();
        } else {
            this.video.pause();
        }
    };

    SRMVPlayer.prototype.onPlay = function () {
        this.wrap.classList.remove('srmvp-paused');
        if (this.poster) this.poster.classList.add('srmvp-hidden');
        if (this.centerPlay && !this.poster) this.centerPlay.style.display = 'none';
        if (this.iconPlay) this.iconPlay.style.display = 'none';
        if (this.iconPause) this.iconPause.style.display = '';
    };

    SRMVPlayer.prototype.onPause = function () {
        this.wrap.classList.add('srmvp-paused');
        if (this.iconPlay) this.iconPlay.style.display = '';
        if (this.iconPause) this.iconPause.style.display = 'none';
    };

    SRMVPlayer.prototype.onEnded = function () {
        this.wrap.classList.add('srmvp-paused');
        if (this.poster) this.poster.classList.remove('srmvp-hidden');
        if (this.iconPlay) this.iconPlay.style.display = '';
        if (this.iconPause) this.iconPause.style.display = 'none';
        if (this.played) this.played.style.width = '0%';
        if (this.thumb) this.thumb.style.left = '0%';
    };

    SRMVPlayer.prototype.onTimeUpdate = function () {
        var video = this.video;
        if (!video.duration) return;
        var pct = (video.currentTime / video.duration) * 100;
        if (this.played) this.played.style.width = pct + '%';
        if (this.thumb) this.thumb.style.left = pct + '%';
        if (this.currentTimeEl) this.currentTimeEl.textContent = formatTime(video.currentTime);
    };

    SRMVPlayer.prototype.onBufferUpdate = function () {
        var video = this.video;
        if (!video.duration || !video.buffered.length) return;
        var pct = (video.buffered.end(video.buffered.length - 1) / video.duration) * 100;
        if (this.buffered) this.buffered.style.width = pct + '%';
    };

    SRMVPlayer.prototype.seek = function (e) {
        if (!this.video.duration || !this.progressBar) return;
        var rect = this.progressBar.getBoundingClientRect();
        var x = (e.clientX || e.pageX) - rect.left;
        var ratio = Math.max(0, Math.min(1, x / rect.width));
        this.video.currentTime = ratio * this.video.duration;
    };

    SRMVPlayer.prototype.muteToggle = function () {
        this.video.muted = !this.video.muted;
        this.updateMuteIcon(this.video.muted);
        if (this.volumeSlider) {
            this.volumeSlider.value = this.video.muted ? 0 : this.video.volume || 1;
        }
    };

    SRMVPlayer.prototype.updateMuteIcon = function (muted) {
        if (this.iconVolume) this.iconVolume.style.display = muted ? 'none' : '';
        if (this.iconMuted) this.iconMuted.style.display = muted ? '' : 'none';
    };

    SRMVPlayer.prototype.toggleFullscreen = function () {
        if (!document.fullscreenElement && !document.webkitFullscreenElement) {
            if (this.wrap.requestFullscreen) {
                this.wrap.requestFullscreen();
            } else if (this.wrap.webkitRequestFullscreen) {
                this.wrap.webkitRequestFullscreen();
            }
        } else {
            if (document.exitFullscreen) {
                document.exitFullscreen();
            } else if (document.webkitExitFullscreen) {
                document.webkitExitFullscreen();
            }
        }
    };

    SRMVPlayer.prototype.onFullscreenChange = function () {
        var isFs = !!(document.fullscreenElement || document.webkitFullscreenElement);
        if (this.iconFs) this.iconFs.style.display = isFs ? 'none' : '';
        if (this.iconExitFs) this.iconExitFs.style.display = isFs ? '' : 'none';
    };

    SRMVPlayer.prototype.initIframe = function () {
        var poster = this.wrap.querySelector('.srmvp-iframe-poster');
        var iframeWrap = this.wrap.querySelector('.srmvp-iframe-wrap');
        var iframe = this.wrap.querySelector('.srmvp-iframe');

        if (!poster || !iframeWrap || !iframe) return;

        poster.addEventListener('click', function () {
            var src = iframe.dataset.src;
            if (src) {
                iframe.src = src;
            }
            iframeWrap.classList.remove('srmvp-iframe-hidden');
            poster.style.display = 'none';
        });
    };

    function initAll() {
        var players = document.querySelectorAll('.srmvp-player-wrap');
        players.forEach(function (wrap) {
            if (!wrap.dataset.srmvpInit) {
                wrap.dataset.srmvpInit = '1';
                new SRMVPlayer(wrap);
            }
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initAll);
    } else {
        initAll();
    }

    window.SRMVPInit = initAll;
})();
