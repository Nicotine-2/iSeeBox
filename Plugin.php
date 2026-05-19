<?php
/**
 * 精致小巧的灯箱 for Typecho 1.3
 * @package iSeeBox
 * @author Nicotine-2
 * @version 1.0.0
 * @link https://github.com/Nicotine-2/iSeeBox
 */

class iSeeBox_Plugin implements Typecho_Plugin_Interface
{
    const VERSION = '1.0.0';
    
    public static function activate()
    {
        Typecho_Plugin::factory('Widget_Archive')->footer = array('iSeeBox_Plugin', 'renderFooter');
        return _t('插件已激活，已添加瀑布流相册支持（优化版）。');
    }
    
    public static function deactivate()
    {
        return _t('插件已禁用。');
    }
    
    public static function config(Typecho_Widget_Helper_Form $form)
    {
        // 范围选择器
        $selectImg = new Typecho_Widget_Helper_Form_Element_Text(
            'selectImg',
            NULL,
            '.entry-content a:has(img), .post-content a:has(img), .article-content a:has(img), .photo-item a, .photo-grid a, .waterfall-grid a',
            _t('范围选择器'),
            _t('根据你所使用的主题而修改。')
        );
        $form->addInput($selectImg);
        
        // 排除选择器
        $excludeSelector = new Typecho_Widget_Helper_Form_Element_Text(
            'excludeSelector',
            NULL,
            '.article-header, .post-header, .entry-header, .post-thumbnail, .thumb-img, .header-image, .list-thumb, .archive-item-thumb, .post-list .thumbnail, .index-post-thumb, .album-card-link, .album-card, .album-cover, a.album-card-link',
            _t('排除选择器'),
            _t('不应用灯箱效果的区域。')
        );
        $form->addInput($excludeSelector);
        
        // 是否自动为图片添加链接
        $autoWrap = new Typecho_Widget_Helper_Form_Element_Radio(
            'autoWrap',
            array(
                'true' => _t('是（推荐）'),
                'false' => _t('否')
            ),
            'true',
            _t('自动为图片添加链接'),
            _t('如果文章中的图片没有被链接包裹，自动为图片添加链接，使其支持灯箱效果。')
        );
        $form->addInput($autoWrap);
        
        // 仅在文章页启用
        $onlySingle = new Typecho_Widget_Helper_Form_Element_Radio(
            'onlySingle',
            array(
                'true' => _t('是'),
                'false' => _t('否')
            ),
            'false',
            _t('仅在文章页启用'),
            _t('<strong style="color:red">注意：如果要让瀑布流相册支持灯箱，请选择"否"</strong>')
        );
        $form->addInput($onlySingle);
        
        // 背景遮罩颜色
        $overlayColor = new Typecho_Widget_Helper_Form_Element_Text(
            'overlayColor',
            NULL,
            '#000000',
            _t('遮罩层颜色'),
            _t('灯箱背景颜色，默认黑色 #000000')
        );
        $form->addInput($overlayColor);
        
        // 遮罩层透明度
        $overlayOpacity = new Typecho_Widget_Helper_Form_Element_Text(
            'overlayOpacity',
            NULL,
            '0.85',
            _t('遮罩层透明度'),
            _t('请输入0-1之间的数字')
        );
        $overlayOpacity->input->setAttribute('class', 'mini');
        $form->addInput($overlayOpacity->addRule('isFloat', _t('请输入0-1之间的数字'))->addRule('required', _t('请设置遮罩层透明度')));
        
        // 白线颜色
        $lineColor = new Typecho_Widget_Helper_Form_Element_Text(
            'lineColor',
            NULL,
            '#ffffff',
            _t('白线/边框颜色'),
            _t('动画边框颜色，默认白色 #ffffff')
        );
        $form->addInput($lineColor);
        
        // 白线宽度
        $lineWidth = new Typecho_Widget_Helper_Form_Element_Text(
            'lineWidth',
            NULL,
            '2',
            _t('白线宽度(px)'),
            _t('边框线条粗细，默认2像素')
        );
        $lineWidth->input->setAttribute('class', 'mini');
        $form->addInput($lineWidth->addRule('isInteger', _t('请输入数字')));
        
        // 白线内边距
        $linePadding = new Typecho_Widget_Helper_Form_Element_Text(
            'linePadding',
            NULL,
            '20',
            _t('边框内边距(px)'),
            _t('白线框比图片大多少像素，默认20像素')
        );
        $linePadding->input->setAttribute('class', 'mini');
        $form->addInput($linePadding->addRule('isInteger', _t('请输入数字')));
        
        // 垂直延伸速度
        $verticalSpeed = new Typecho_Widget_Helper_Form_Element_Text(
            'verticalSpeed',
            NULL,
            '500',
            _t('垂直延伸速度(ms)'),
            _t('白线上下延伸的动画时长，默认500毫秒')
        );
        $verticalSpeed->input->setAttribute('class', 'mini');
        $form->addInput($verticalSpeed->addRule('isInteger', _t('请输入数字')));
        
        // 水平展开速度
        $horizontalSpeed = new Typecho_Widget_Helper_Form_Element_Text(
            'horizontalSpeed',
            NULL,
            '500',
            _t('水平展开速度(ms)'),
            _t('白线左右展开的动画时长，默认500毫秒')
        );
        $horizontalSpeed->input->setAttribute('class', 'mini');
        $form->addInput($horizontalSpeed->addRule('isInteger', _t('请输入数字')));
        
        // 图片渐显速度
        $fadeSpeed = new Typecho_Widget_Helper_Form_Element_Text(
            'fadeSpeed',
            NULL,
            '500',
            _t('图片渐显速度(ms)'),
            _t('图片淡入的动画时长，默认500毫秒')
        );
        $fadeSpeed->input->setAttribute('class', 'mini');
        $form->addInput($fadeSpeed->addRule('isInteger', _t('请输入数字')));
        
        // 图片最大占比
        $imageMaxSize = new Typecho_Widget_Helper_Form_Element_Text(
            'imageMaxSize',
            NULL,
            '80',
            _t('图片最大占比(%)'),
            _t('图片占屏幕宽高的最大百分比，默认80%')
        );
        $imageMaxSize->input->setAttribute('class', 'mini');
        $form->addInput($imageMaxSize->addRule('isInteger', _t('请输入数字')));
        
        // 是否加载 jQuery
        $jquerySelect = new Typecho_Widget_Helper_Form_Element_Radio(
            'jquerySelect',
            array(
                'local' => _t('加载本地jQuery（推荐）'),
                'cdn' => _t('加载CDN jQuery'),
                'none' => _t('不加载（主题已包含）')
            ),
            'local',
            _t('加载 jQuery 库')
        );
        $form->addInput($jquerySelect);
        
        // jQuery 版本（当选择CDN时显示）
        $jqueryVersion = new Typecho_Widget_Helper_Form_Element_Select(
            'jqueryVersion',
            array(
                '3.7.1' => 'jQuery 3.7.1 (最新)',
                '3.6.4' => 'jQuery 3.6.4',
                '3.5.1' => 'jQuery 3.5.1'
            ),
            '3.7.1',
            _t('CDN jQuery 版本（仅在选择CDN时生效）')
        );
        $form->addInput($jqueryVersion);
        
        // 优化选项
        $debounceDelay = new Typecho_Widget_Helper_Form_Element_Text(
            'debounceDelay',
            NULL,
            '300',
            _t('防抖延迟(ms)'),
            _t('防止频繁触发绑定，单位毫秒，默认300ms')
        );
        $debounceDelay->input->setAttribute('class', 'mini');
        $form->addInput($debounceDelay->addRule('isInteger', _t('请输入数字')));
        
        $batchProcessLimit = new Typecho_Widget_Helper_Form_Element_Text(
            'batchProcessLimit',
            NULL,
            '50',
            _t('批量处理限制'),
            _t('单次处理的最大图片数量，避免长时间阻塞主线程，默认50张')
        );
        $batchProcessLimit->input->setAttribute('class', 'mini');
        $form->addInput($batchProcessLimit->addRule('isInteger', _t('请输入数字')));
    }
    
    public static function personalConfig(Typecho_Widget_Helper_Form $form)
    {
    }
    
    public static function renderFooter()
    {
        $settings = Helper::options()->plugin('iSeeBox');
        $pluginUrl = Helper::options()->pluginUrl . '/iSeeBox/';
        $version = self::VERSION;
        
        $output = array();
        
        $output[] = '<link rel="stylesheet" type="text/css" href="' . $pluginUrl . 'css/iseebox.css?v=' . $version . '" />';
        
        // 根据设置决定如何加载jQuery
        if ($settings->jquerySelect == 'local') {
            // 加载本地jQuery
            $output[] = '<script type="text/javascript" src="' . $pluginUrl . 'js/jquery.min.js?v=' . $version . '"></script>';
        } elseif ($settings->jquerySelect == 'cdn') {
            // 加载CDN jQuery
            $jqueryVersion = $settings->jqueryVersion ?: '3.7.1';
            $jqueryUrl = 'https://cdn.bootcdn.net/ajax/libs/jquery/' . $jqueryVersion . '/jquery.min.js';
            $output[] = '<script type="text/javascript" src="' . $jqueryUrl . '"></script>';
        }
        // 如果选择'none'则不加载jQuery
        
        $output[] = '<script type="text/javascript" src="' . $pluginUrl . 'js/iseebox.js?v=' . $version . '"></script>';
        $output[] = self::getInitScript($settings);
        
        echo implode("\n", $output) . "\n";
    }
    
    private static function getInitScript($settings)
    {
        $onlySingle = $settings->onlySingle == 'true';
        $selectors = $settings->selectImg;
        
        if (empty($selectors)) {
            $selectors = '.entry-content a:has(img), .post-content a:has(img), .article-content a:has(img), .photo-item a, .photo-grid a';
        }
        
        $excludeSelector = trim($settings->excludeSelector);
        $excludeSelectorsArray = array();
        if (!empty($excludeSelector)) {
            $excludeSelectorsArray = array_map('trim', explode(',', $excludeSelector));
        }
        
        $autoWrap = $settings->autoWrap == 'true';
        $overlayColor = $settings->overlayColor ?: '#000000';
        $overlayOpacity = floatval($settings->overlayOpacity);
        $lineColor = $settings->lineColor ?: '#ffffff';
        $lineWidth = intval($settings->lineWidth);
        $linePadding = intval($settings->linePadding);
        $verticalSpeed = intval($settings->verticalSpeed);
        $horizontalSpeed = intval($settings->horizontalSpeed);
        $fadeSpeed = intval($settings->fadeSpeed);
        $imageMaxSize = intval($settings->imageMaxSize);
        $loop = $settings->loop == 'true';
        $debounceDelay = isset($settings->debounceDelay) ? intval($settings->debounceDelay) : 300;
        $batchProcessLimit = isset($settings->batchProcessLimit) ? intval($settings->batchProcessLimit) : 50;
        
        $config = array(
            'overlayColor' => $overlayColor,
            'overlayOpacity' => $overlayOpacity,
            'lineColor' => $lineColor,
            'lineWidth' => $lineWidth,
            'linePadding' => $linePadding,
            'verticalSpeed' => $verticalSpeed,
            'horizontalSpeed' => $horizontalSpeed,
            'fadeSpeed' => $fadeSpeed,
            'imageMaxSize' => $imageMaxSize,
            'loop' => $loop
        );
        
        $configJson = json_encode($config);
        $excludeSelectorsJson = json_encode($excludeSelectorsArray);
        $autoWrapStr = $autoWrap ? 'true' : 'false';
        
        $pageCheck = '';
        if ($onlySingle) {
            $pageCheck = <<<'PAGECHECK'
            var isSingle = document.body.classList.contains('post') || 
                          document.body.classList.contains('page') ||
                          document.querySelector('article.post, .post-page, .single');
            if (!isSingle) return;
PAGECHECK;
        }
        
        $script = <<<JS
<script type="text/javascript">
(function() {
    var iseeConfig = {$configJson};
    var boundElements = new Set(); // 使用Set跟踪已绑定的元素
    
    // 防抖函数
    function debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }

    // 节流函数
    function throttle(func, limit) {
        let inThrottle;
        return function() {
            const args = arguments;
            const context = this;
            if (!inThrottle) {
                func.apply(context, args);
                inThrottle = true;
                setTimeout(() => inThrottle = false, limit);
            }
        }
    }

    // 批量处理函数，避免长时间阻塞主线程
    function batchProcess(items, processFn, batchSize = {$batchProcessLimit}) {
        if (!items.length) return Promise.resolve();
        
        return new Promise((resolve) => {
            let index = 0;
            
            function processBatch() {
                const endIndex = Math.min(index + batchSize, items.length);
                
                for (let i = index; i < endIndex; i++) {
                    processFn(items[i]);
                }
                
                index = endIndex;
                
                if (index < items.length) {
                    // 使用 requestIdleCallback 或 setTimeout 将下一个批次推迟到下一个事件循环
                    if (window.requestIdleCallback) {
                        requestIdleCallback(processBatch);
                    } else {
                        setTimeout(processBatch, 0);
                    }
                } else {
                    resolve();
                }
            }
            
            processBatch();
        });
    }

    // 检查元素是否已被绑定
    function isElementBound(element) {
        return boundElements.has(element);
    }

    // 标记元素为已绑定
    function markAsBound(element) {
        boundElements.add(element);
    }

    // 快速绑定灯箱 - 优化版本
    function bindLightbox() {
        if (typeof jQuery === 'undefined' || typeof jQuery.fn.iseebox === 'undefined') {
            setTimeout(bindLightbox, 10);
            return;
        }
        
        jQuery(function($) {
            {$pageCheck}
            
            var autoWrapEnabled = {$autoWrapStr};
            if (autoWrapEnabled) {
                var excludeSelectors = {$excludeSelectorsJson};
                
                var images = $('.entry-content img, .post-content img, .article-content img, .photo-item img, .photo-grid img, .waterfall-grid img');
                
                // 批量处理图片包装
                batchProcess(images, function(img) {
                    var \$img = $(img);
                    var src = \$img.attr('src') || \$img.attr('data-src') || \$img.attr('data-original');
                    
                    var isExcluded = false;
                    if (excludeSelectors.length > 0) {
                        for (var i = 0; i < excludeSelectors.length; i++) {
                            if (excludeSelectors[i] && \$img.closest(excludeSelectors[i]).length > 0) {
                                isExcluded = true;
                                break;
                            }
                        }
                    }
                    
                    if (!isExcluded && \$img.parent('a').length === 0 && src) {
                        if (src.match(/\\.(jpg|jpeg|png|gif|webp|bmp|svg)/i)) {
                            var imgAlt = \$img.attr('alt') || '';
                            \$img.wrap('<a href="' + src + '" title="' + imgAlt + '" class="isee-auto-link"></a>');
                        }
                    }
                });
            }
            
            var selectorStr = "{$selectors}, .isee-auto-link, .photo-item a, .photo-grid a, .waterfall-grid a";
            var \$links = $(selectorStr);
            
            var excludeSelectors = {$excludeSelectorsJson};
            if (excludeSelectors.length > 0) {
                \$links = \$links.not(function() {
                    var \$this = $(this);
                    for (var i = 0; i < excludeSelectors.length; i++) {
                        if (excludeSelectors[i] && (\$this.closest(excludeSelectors[i]).length > 0 || \$this.parents(excludeSelectors[i]).length > 0)) {
                            return true;
                        }
                    }
                    return false;
                });
            }
            
            // 过滤掉相册卡片链接
            \$links = \$links.not(function() {
                var \$this = $(this);
                var hasAlbumClass = \$this.hasClass('album-card-link') || 
                                   \$this.closest('.album-card-link').length > 0 ||
                                   \$this.parents('.album-card-link').length > 0;
                return hasAlbumClass;
            });
            
            // 过滤掉已绑定的元素
            var unboundLinks = \$links.filter(function() {
                return !isElementBound(this);
            });
            
            if (unboundLinks.length > 0) {
                try {
                    // 批量绑定灯箱
                    batchProcess(unboundLinks, function(link) {
                        var \$link = $(link);
                        \$link.off('click.isee');
                        \$link.iseebox(iseeConfig);
                        markAsBound(link);
                    });
                    
                    if (window.console) console.log('iSeeBox: 新增绑定 ' + unboundLinks.length + ' 个图片');
                } catch(e) {
                    if (window.console) console.error('iSeeBox 错误:', e);
                }
            }
        });
    }
    
    // 防抖后的绑定函数
    var debouncedBind = debounce(bindLightbox, {$debounceDelay});
    
    // 页面加载后立即绑定
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', bindLightbox);
    } else {
        bindLightbox();
    }
    
    // 优化的 MutationObserver - 只关注图片相关的变动
    var observer = null;
    var initObserver = function() {
        if (observer) {
            observer.disconnect();
        }
        
        observer = new MutationObserver(debounce(function(mutations) {
            var hasImageChanges = false;
            
            for (var i = 0; i < mutations.length; i++) {
                var mutation = mutations[i];
                
                if (mutation.type === 'childList') {
                    // 检查新增节点
                    for (var j = 0; j < mutation.addedNodes.length; j++) {
                        var node = mutation.addedNodes[j];
                        
                        if (node.nodeType === 1) { // 元素节点
                            // 检查是否包含图片或链接
                            if (node.tagName === 'IMG' || 
                                node.tagName === 'A' && node.querySelector('img') ||
                                node.querySelector('img') || 
                                node.querySelector('a:has(img)')) {
                                hasImageChanges = true;
                                break;
                            }
                        }
                    }
                    
                    if (hasImageChanges) {
                        break;
                    }
                }
            }
            
            if (hasImageChanges) {
                // 延迟执行，确保DOM完全渲染
                setTimeout(debouncedBind, 100);
            }
        }, 200)); // MutationObserver 内部也使用防抖
        
        if (document.body) {
            observer.observe(document.body, { 
                childList: true, 
                subtree: true 
            });
        } else {
            document.addEventListener('DOMContentLoaded', function() {
                observer.observe(document.body, { 
                    childList: true, 
                    subtree: true 
                });
            });
        }
    };
    
    // 初始化观察器
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initObserver);
    } else {
        initObserver();
    }
    
    // 为图片加载事件添加节流处理
    var handleImageLoad = throttle(function() {
        debouncedBind();
    }, 500);
    
    // 监听图片加载完成事件，使用事件委托
    document.addEventListener('load', function(e) {
        if (e.target.tagName === 'IMG') {
            handleImageLoad();
        }
    }, true); // 使用捕获阶段确保能捕获到
    
    // 瀑布流可能通过AJAX等方式动态加载，监听页面滚动事件作为备选方案
    var scrollHandler = throttle(function() {
        // 只在页面底部附近滚动时检查
        if ((window.innerHeight + window.scrollY) >= document.body.offsetHeight - 1000) {
            debouncedBind();
        }
    }, 1000); // 滚动事件节流
    
    window.addEventListener('scroll', scrollHandler);
    
    // 页面显示/隐藏时重新检查
    if ('hidden' in document) {
        document.addEventListener('visibilitychange', function() {
            if (!document.hidden) {
                setTimeout(debouncedBind, 500);
            }
        });
    }
    
    // 如果页面有 Pjax 或类似功能，提供手动触发接口
    window.iSeeBoxRefresh = debouncedBind;
    
})();
</script>
JS;
        
        return $script;
    }
}