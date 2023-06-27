import Plugin from 'src/plugin-system/plugin.class';

export default class ImageRotatorPlugin extends Plugin {
    static options = {
        imgSelector: 'img',
        activeClass: 'active',
        interval: 3000
    }

    init() {
        this.currentImage = 0;
        this._getImages();
        this._startRotation();
    }

    _getImages() {
        this.images = this.el.querySelectorAll(this.options.imgSelector)
    }

    _startRotation() {
        if (this.images.length <= 1 || this.interval) {
            return;
        }

        this.interval = setInterval(this._switchImage.bind(this), this.options.interval);
    }

    _switchImage() {
        if (this.currentImage >= this.images.length - 1) {
            this.currentImage = 0;
        } else {
            this.currentImage++;
        }

        this._deactivateImages();
        this._activateCurrentImage();
    }

    _deactivateImages() {
        this.images.forEach((image) => {
            image.classList.remove(this.options.activeClass);
        });
    }

    _activateCurrentImage() {
        this.images[this.currentImage].classList.add(this.options.activeClass);
    }
}
