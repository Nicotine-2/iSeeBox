/* iseebox.js - iSeeBox µÆÏäºËÐÄ v2.0 (ÎÞ°´Å¥°æ) */
(function(w, $) {
    'use strict';
    
    var currentIndex = -1;
    var items = [];
    var config = {};
    var isActive = false;
    
    var overlay, whiteBoard, imageContainer, image;
    var verticalLine, horizontalLine, centerDot;
    
    var frameRect = { width: 0, height: 0, left: 0, top: 0, centerX: 0, centerY: 0 };
    var clickPos = { left: 0, top: 0 };
    var currentDisplaySize = { width: 0, height: 0 };
    
    function createDOM() {
        if (document.getElementById('isee-overlay')) return;
        
        overlay = $('<div id="isee-overlay"></div>');
        whiteBoard = $('<div id="isee-white-board"></div>');
        centerDot = $('<div id="isee-center-dot"></div>');
        verticalLine = $('<div id="isee-vertical-line"></div>');
        horizontalLine = $('<div id="isee-horizontal-line"></div>');
        imageContainer = $('<div id="isee-image-container"></div>');
        image = $('<img id="isee-image">');
        
        imageContainer.append(image);
        
        $('body').append(overlay, whiteBoard, centerDot, verticalLine, horizontalLine, imageContainer);
        
        overlay.on('click', close);
        image.on('load', onImageLoad);
        image.on('error', onImageError);
    }
    
    function calculateDisplaySize(naturalWidth, naturalHeight) {
        var maxSizePercent = config.imageMaxSize || 80;
        var windowWidth = $(window).width();
        var windowHeight = $(window).height();
        var maxWidth = windowWidth * maxSizePercent / 100;
        var maxHeight = windowHeight * maxSizePercent / 100;
        var ratio = Math.min(maxWidth / naturalWidth, maxHeight / naturalHeight);
        if (ratio > 1) ratio = 1;
        return {
            width: naturalWidth * ratio,
            height: naturalHeight * ratio
        };
    }
    
    function onImageLoad() {
        var naturalWidth = image[0].naturalWidth;
        var naturalHeight = image[0].naturalHeight;
        currentDisplaySize = calculateDisplaySize(naturalWidth, naturalHeight);
        
        frameRect = calculateFrameRect(currentDisplaySize);
        
        whiteBoard.css({
            left: frameRect.left,
            top: frameRect.top,
            width: frameRect.width,
            height: frameRect.height
        });
        
        image.css({
            width: currentDisplaySize.width,
            height: currentDisplaySize.height
        });
        
        imageContainer.css({
            width: currentDisplaySize.width,
            height: currentDisplaySize.height,
            left: frameRect.centerX,
            top: frameRect.centerY,
            transform: 'translate(-50%, -50%)'
        });
        
        imageContainer.addClass('show');
    }
    
    function onImageError() {
        image.attr('src', 'data:image/svg+xml,%3Csvg xmlns="http://www.w3.org/2000/svg" width="200" height="200" viewBox="0 0 24 24" fill="none" stroke="%23999"%3E%3Crect x="3" y="3" width="18" height="18" rx="2"%3E%3C/rect%3E%3Ccircle cx="8.5" cy="8.5" r="1.5"%3E%3C/circle%3E%3Cpath d="M21 15L16 10L5 21"%3E%3C/path%3E%3C/svg%3E');
        setTimeout(function() { imageContainer.addClass('show'); }, 100);
    }
    
    function calculateFrameRect(displaySize) {
        var padding = config.linePadding || 20;
        var windowWidth = $(window).width();
        var windowHeight = $(window).height();
        var frameWidth = displaySize.width + padding * 2;
        var frameHeight = displaySize.height + padding * 2;
        var frameLeft = (windowWidth - frameWidth) / 2;
        var frameTop = (windowHeight - frameHeight) / 2;
        return {
            width: frameWidth,
            height: frameHeight,
            left: frameLeft,
            top: frameTop,
            centerX: frameLeft + frameWidth / 2,
            centerY: frameTop + frameHeight / 2
        };
    }
    
    function getClickPosition($clickImg) {
        var offset = $clickImg.offset();
        var width = $clickImg.width();
        var height = $clickImg.height();
        return {
            left: offset.left + width / 2,
            top: offset.top + height / 2
        };
    }
    
    function getTargetDisplaySize($clickImg) {
        var naturalWidth = $clickImg[0].naturalWidth;
        var naturalHeight = $clickImg[0].naturalHeight;
        if (!naturalWidth || naturalWidth === 0) {
            naturalWidth = $clickImg.width();
            naturalHeight = $clickImg.height();
        }
        return calculateDisplaySize(naturalWidth, naturalHeight);
    }
    
    function resetAnimationElements() {
        centerDot.stop(true, true).css('display', 'none').css('opacity', '1');
        verticalLine.stop(true, true).css('display', 'none').css('height', '0');
        horizontalLine.stop(true, true).css('display', 'none').css('width', '0');
    }
    
    function animateStage1(callback) {
        var lineColor = config.lineColor || '#ffffff';
        var lineWidth = config.lineWidth || 2;
        var verticalSpeed = config.verticalSpeed || 500;
        
        centerDot.css({
            display: 'block',
            position: 'fixed',
            left: clickPos.left - lineWidth / 2,
            top: clickPos.top - lineWidth / 2,
            width: lineWidth,
            height: lineWidth,
            backgroundColor: lineColor,
            zIndex: 10000
        });
        
        verticalLine.css({
            display: 'block',
            position: 'fixed',
            left: frameRect.centerX - lineWidth / 2,
            top: frameRect.centerY,
            width: lineWidth,
            height: 0,
            backgroundColor: lineColor,
            zIndex: 10000
        });
        
        centerDot[0].offsetHeight;
        verticalLine[0].offsetHeight;
        
        centerDot.animate({
            left: frameRect.centerX - lineWidth / 2,
            top: frameRect.centerY - lineWidth / 2
        }, verticalSpeed, 'swing');
        
        verticalLine.animate({
            height: frameRect.height,
            top: frameRect.top
        }, verticalSpeed, 'swing', function() {
            centerDot.fadeOut(100);
            if (callback) callback();
        });
    }
    
    function animateStage2(callback) {
        var lineColor = config.lineColor || '#ffffff';
        var horizontalSpeed = config.horizontalSpeed || 500;
        
        verticalLine.css('display', 'none');
        
        horizontalLine.css({
            display: 'block',
            position: 'fixed',
            left: frameRect.centerX,
            top: frameRect.top,
            width: 0,
            height: frameRect.height,
            backgroundColor: lineColor,
            zIndex: 10000
        });
        
        horizontalLine[0].offsetHeight;
        
        horizontalLine.animate({
            width: frameRect.width,
            left: frameRect.centerX - frameRect.width / 2
        }, horizontalSpeed, 'swing', function() {
            horizontalLine.css('display', 'none');
            
            whiteBoard.css({
                display: 'block',
                position: 'fixed',
                left: frameRect.left,
                top: frameRect.top,
                width: frameRect.width,
                height: frameRect.height,
                backgroundColor: lineColor,
                zIndex: 10000,
                opacity: 1
            });
            
            if (callback) callback();
        });
    }
    
    function showImage(imageUrl) {
        var fadeSpeed = config.fadeSpeed || 500;
        
        imageContainer.css({
            position: 'fixed',
            left: frameRect.centerX,
            top: frameRect.centerY,
            transform: 'translate(-50%, -50%)',
            zIndex: 10001,
            opacity: 0,
            visibility: 'visible',
            width: currentDisplaySize.width,
            height: currentDisplaySize.height
        });
        
        image.attr('src', imageUrl);
        
        var checkImageLoaded = setInterval(function() {
            if (image[0].complete && image[0].naturalWidth > 0) {
                clearInterval(checkImageLoaded);
                imageContainer.animate({ opacity: 1 }, fadeSpeed);
            }
        }, 50);
        
        setTimeout(function() {
            clearInterval(checkImageLoaded);
            imageContainer.animate({ opacity: 1 }, fadeSpeed);
        }, fadeSpeed + 1000);
    }
    
    function open(index, $clickImg) {
        if (isActive) close();
        if (!items || items.length === 0) return;
        
        isActive = true;
        currentIndex = (index >= 0 && index < items.length) ? index : 0;
        var item = items[currentIndex];
        
        resetAnimationElements();
        clickPos = getClickPosition($clickImg);
        var displaySize = getTargetDisplaySize($clickImg);
        frameRect = calculateFrameRect(displaySize);
        currentDisplaySize = displaySize;
        
        whiteBoard.css({
            display: 'none',
            left: frameRect.left,
            top: frameRect.top,
            width: frameRect.width,
            height: frameRect.height
        });
        
        imageContainer.css({
            position: 'fixed',
            left: frameRect.centerX,
            top: frameRect.centerY,
            transform: 'translate(-50%, -50%)',
            width: currentDisplaySize.width,
            height: currentDisplaySize.height,
            opacity: 0,
            visibility: 'hidden'
        });
        
        image.css({
            width: currentDisplaySize.width,
            height: currentDisplaySize.height
        });
        
        overlay.css({
            backgroundColor: config.overlayColor || '#000000',
            opacity: config.overlayOpacity || 0.85
        }).addClass('show');
        
        animateStage1(function() {
            animateStage2(function() {
                showImage(item.href);
            });
        });
    }
    
    function close() {
        if (!isActive) return;
        isActive = false;
        overlay.removeClass('show');
        whiteBoard.css('display', 'none');
        centerDot.css('display', 'none');
        verticalLine.css('display', 'none');
        horizontalLine.css('display', 'none');
        imageContainer.css({ opacity: 0, visibility: 'hidden' });
        setTimeout(function() { image.attr('src', ''); }, 100);
    }
    
    $.iseebox = function(data, index, opts, clickElement) {
        items = [];
        if (Array.isArray(data)) {
            for (var i = 0; i < data.length; i++) {
                var item = data[i];
                if (typeof item === 'string') {
                    items.push({ href: item, title: '' });
                } else if (item && item.href) {
                    items.push({ href: item.href, title: item.title || '' });
                } else if (item && item.src) {
                    items.push({ href: item.src, title: item.title || '' });
                }
            }
        } else if (typeof data === 'string') {
            items.push({ href: data, title: '' });
        } else if (data && data.href) {
            items.push({ href: data.href, title: data.title || '' });
        }
        
        items = items.filter(function(item) {
            return item.href && item.href.match(/\.(jpg|jpeg|png|gif|webp|bmp|svg)/i);
        });
        if (items.length === 0) return false;
        
        config = $.extend({
            overlayColor: '#000000',
            overlayOpacity: 0.85,
            lineColor: '#ffffff',
            lineWidth: 2,
            linePadding: 20,
            verticalSpeed: 500,
            horizontalSpeed: 500,
            fadeSpeed: 500,
            imageMaxSize: 80,
            loop: true
        }, opts);
        
        createDOM();
        var startIndex = (typeof index === 'number' && index >= 0 && index < items.length) ? index : 0;
        var $clickImg = $(clickElement).find('img');
        open(startIndex, $clickImg);
        return false;
    };
    
    $.fn.iseebox = function(opts) {
        var self = this;
        var settings = opts || {};
        self.each(function() {
            var $this = $(this);
            var href = $this.attr('href');
            var $img = $this.find('img');
            var title = $img.attr('alt') || $img.attr('title') || $this.attr('title') || '';
            if (href && href.match(/\.(jpg|jpeg|png|gif|webp|bmp|svg)/i)) {
                $this.off('click.isee').on('click.isee', function(e) {
                    e.preventDefault();
                    var groupItems = [];
                    var groupSelector = settings.group || self.selector;
                    $(groupSelector).each(function() {
                        var $el = $(this);
                        var imgHref = $el.attr('href');
                        var $imgEl = $el.find('img');
                        var imgTitle = $imgEl.attr('alt') || $imgEl.attr('title') || $el.attr('title') || '';
                        if (imgHref && imgHref.match(/\.(jpg|jpeg|png|gif|webp|bmp|svg)/i)) {
                            groupItems.push({ href: imgHref, title: imgTitle });
                        }
                    });
                    var idx = -1;
                    for (var i = 0; i < groupItems.length; i++) {
                        if (groupItems[i].href === href) { idx = i; break; }
                    }
                    if (groupItems.length > 0) {
                        $.iseebox(groupItems, idx, settings, this);
                    } else {
                        $.iseebox({ href: href, title: title }, 0, settings, this);
                    }
                });
            }
        });
        return self;
    };
    
})(window, jQuery);