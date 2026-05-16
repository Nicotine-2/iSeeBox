<?php
/**
 * 精致小巧的灯箱 for Typecho 1.3
 * 
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
        return _t('插件已激活，已添加瀑布流相册支持。');
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
                'true' => _t('加载'),
                'false' => _t('不加载（主题已包含）')
            ),
            'true',
            _t('加载 jQuery 库')
        );
        $form->addInput($jquerySelect);
        
        // jQuery 版本
        $jqueryVersion = new Typecho_Widget_Helper_Form_Element_Select(
            'jqueryVersion',
            array(
                '3.7.1' => 'jQuery 3.7.1 (最新)',
                '3.6.4' => 'jQuery 3.6.4',
                '3.5.1' => 'jQuery 3.5.1'
            ),
            '3.7.1',
            _t('jQuery 版本')
        );
        $form->addInput($jqueryVersion);
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
        
        if ($settings->jquerySelect == 'true') {
            $jqueryVersion = $settings->jqueryVersion ?: '3.7.1';
            $jqueryUrl = 'https://cdn.bootcdn.net/ajax/libs/jquery/' . $jqueryVersion . '/jquery.min.js';
            $output[] = '<script type="text/javascript" src="' . $jqueryUrl . '"></script>';
        }
        
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
    var isInitialized = false;
    
    function initiSeeBox() {
        if (isInitialized) return;
        if (typeof jQuery === 'undefined' || typeof jQuery.fn.iseebox === 'undefined') {
            setTimeout(initiSeeBox, 100);
            return;
        }
        
        jQuery(function($) {
            {$pageCheck}
            
            var autoWrapEnabled = {$autoWrapStr};
            if (autoWrapEnabled) {
                var excludeSelectors = {$excludeSelectorsJson};
                
                $('.entry-content img, .post-content img, .article-content img, .photo-item img, .photo-grid img').each(function() {
                    var \$img = $(this);
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
            
            var selectorStr = "{$selectors}, .isee-auto-link, .photo-item a, .photo-grid a";
            var \$links = $(selectorStr);
            
            var excludeSelectors = {$excludeSelectorsJson};
            if (excludeSelectors.length > 0) {
                \$links = \$links.filter(function() {
                    var \$this = $(this);
                    for (var i = 0; i < excludeSelectors.length; i++) {
                        if (excludeSelectors[i] && (\$this.closest(excludeSelectors[i]).length > 0 || \$this.parents(excludeSelectors[i]).length > 0)) {
                            return false;
                        }
                    }
                    return true;
                });
            }
            
            \$links = \$links.filter(function() {
                var \$this = $(this);
                var hasAlbumClass = \$this.hasClass('album-card-link') || 
                                   \$this.closest('.album-card-link').length > 0 ||
                                   \$this.parents('.album-card-link').length > 0;
                return !hasAlbumClass;
            });
            
            if (\$links.length > 0) {
                try {
                    \$links.iseebox(iseeConfig);
                    isInitialized = true;
                    if (window.console) console.log('iSeeBox: 初始化完成，共绑定 ' + \$links.length + ' 个图片链接');
                } catch(e) {
                    if (window.console) console.error('iSeeBox 初始化失败:', e);
                }
            }
        });
    }
    
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initiSeeBox);
    } else {
        initiSeeBox();
    }
})();
</script>
JS;
        
        return $script;
    }
}