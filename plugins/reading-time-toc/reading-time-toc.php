<?php
/**
 * Plugin Name: Reading Time + TOC
 * Description: Hiển thị thời gian đọc ước tính và tự tạo Mục lục (TOC) từ H2/H3. Smooth scroll + highlight heading. Có auto-inject và template tag.
 * Version: 1.0.0
 * Author: tqduy02
 */

if (!defined('ABSPATH')) exit;

final class RTOTOC {
  const OPT_KEY = 'rtotoc_options';

  public function __construct() {
    // Admin
    add_action('admin_menu',  [$this, 'add_settings_page']);
    add_action('admin_init',  [$this, 'register_settings']);

    // Front: auto inject + assets
    add_filter('the_content', [$this, 'maybe_inject_into_content'], 9);
    add_action('wp_enqueue_scripts', [$this, 'maybe_enqueue_assets']);
  }

  /* ==================== OPTIONS ==================== */
  public static function defaults() {
    return [
      'enable_reading_time' => 1,
      'wpm'                 => 200,  // từ/phút
      'enable_toc'          => 1,
      'toc_position'        => 'before', // before | after | manual
      'heading_levels'      => ['h2','h3'],
      'min_headings'        => 2,
      'enqueue_assets'      => 1,    // nạp CSS/JS kèm plugin
      'toc_title'           => 'Table of Contents',
      'prepend_label'       => 'Estimated reading time:',
      'icon_clock'          => '⏱',
      'smooth_offset'       => 0, // px trừ đi khi scroll (ví dụ header fixed)
    ];
  }

  public static function opts() {
    return wp_parse_args(get_option(self::OPT_KEY, []), self::defaults());
  }

  /* ==================== ADMIN SETTINGS ==================== */
  public function add_settings_page() {
    add_options_page('Reading Time + TOC', 'Reading Time + TOC', 'manage_options', 'rtotoc', [$this,'render_settings']);
  }

  public function register_settings() {
    register_setting('rtotoc_group', self::OPT_KEY, function($input){
      $o = self::opts();
      $o['enable_reading_time'] = !empty($input['enable_reading_time']) ? 1 : 0;
      $o['wpm']                 = max(50, intval($input['wpm'] ?? 200));
      $o['enable_toc']          = !empty($input['enable_toc']) ? 1 : 0;

      $allowed_pos = ['before','after','manual'];
      $o['toc_position'] = in_array($input['toc_position'] ?? '', $allowed_pos, true) ? $input['toc_position'] : 'before';

      // heading levels
      $levels = isset($input['heading_levels']) && is_array($input['heading_levels']) ? array_map('strtolower', $input['heading_levels']) : ['h2','h3'];
      $levels = array_values(array_intersect($levels, ['h1','h2','h3','h4','h5','h6']));
      $o['heading_levels'] = !empty($levels) ? $levels : ['h2','h3'];

      $o['min_headings']   = max(1, intval($input['min_headings'] ?? 2));
      $o['enqueue_assets'] = !empty($input['enqueue_assets']) ? 1 : 0;

      $o['toc_title']      = sanitize_text_field($input['toc_title'] ?? 'Table of Contents');
      $o['prepend_label']  = sanitize_text_field($input['prepend_label'] ?? 'Estimated reading time:');
      $o['icon_clock']     = sanitize_text_field($input['icon_clock'] ?? '⏱');
      $o['smooth_offset']  = intval($input['smooth_offset'] ?? 0);

      return $o;
    });
  }

  public function render_settings() {
    $o = self::opts(); ?>
    <div class="wrap">
      <h1>Reading Time + TOC</h1>
      <form method="post" action="options.php">
        <?php settings_fields('rtotoc_group'); $key = self::OPT_KEY; ?>
        <table class="form-table" role="presentation">
          <tr><th colspan="2"><h2>Reading Time</h2></th></tr>
          <tr>
            <th scope="row">Bật Reading Time</th>
            <td><label><input type="checkbox" name="<?php echo esc_attr($key); ?>[enable_reading_time]" value="1" <?php checked($o['enable_reading_time'],1); ?>> Enable</label></td>
          </tr>
          <tr>
            <th scope="row">Words per minute</th>
            <td>
              <input type="number" name="<?php echo esc_attr($key); ?>[wpm]" value="<?php echo esc_attr($o['wpm']); ?>" min="50" step="10" style="width:100px">
              <p class="description">Số từ/phút để ước tính (mặc định 200).</p>
            </td>
          </tr>
          <tr>
            <th scope="row">Label & Icon</th>
            <td>
              <input type="text" name="<?php echo esc_attr($key); ?>[prepend_label]" value="<?php echo esc_attr($o['prepend_label']); ?>" style="width:260px">
              &nbsp; Icon: <input type="text" name="<?php echo esc_attr($key); ?>[icon_clock]" value="<?php echo esc_attr($o['icon_clock']); ?>" style="width:80px">
            </td>
          </tr>

          <tr><th colspan="2"><h2>Table of Contents (TOC)</h2></th></tr>

          <tr>
            <th scope="row">Bật TOC</th>
            <td><label><input type="checkbox" name="<?php echo esc_attr($key); ?>[enable_toc]" value="1" <?php checked($o['enable_toc'],1); ?>> Enable</label></td>
          </tr>
          <tr>
            <th scope="row">Vị trí chèn</th>
            <td>
              <select name="<?php echo esc_attr($key); ?>[toc_position]">
                <option value="before" <?php selected($o['toc_position'],'before'); ?>>Trước nội dung</option>
                <option value="after"  <?php selected($o['toc_position'],'after'); ?>>Sau nội dung</option>
                <option value="manual" <?php selected($o['toc_position'],'manual'); ?>>Tự chèn bằng template tag</option>
              </select>
              <p class="description">Chọn “manual” nếu bạn sẽ gọi bằng PHP trong theme.</p>
            </td>
          </tr>
          <tr>
            <th scope="row">Mức heading</th>
            <td>
              <?php foreach(['h1','h2','h3','h4','h5','h6'] as $lv): ?>
                <label style="margin-right:10px">
                  <input type="checkbox" name="<?php echo esc_attr($key); ?>[heading_levels][]" value="<?php echo esc_attr($lv); ?>"
                    <?php checked(in_array($lv,$o['heading_levels'],true)); ?>> <?php echo strtoupper($lv); ?>
                </label>
              <?php endforeach; ?>
              <p class="description">Khuyên dùng: H2/H3.</p>
            </td>
          </tr>
          <tr>
            <th scope="row">Tối thiểu heading</th>
            <td>
              <input type="number" name="<?php echo esc_attr($key); ?>[min_headings]" value="<?php echo esc_attr($o['min_headings']); ?>" min="1" style="width:80px">
            </td>
          </tr>
          <tr>
            <th scope="row">Tiêu đề TOC</th>
            <td><input type="text" name="<?php echo esc_attr($key); ?>[toc_title]" value="<?php echo esc_attr($o['toc_title']); ?>" style="width:280px"></td>
          </tr>
          <tr>
            <th scope="row">Smooth scroll offset (px)</th>
            <td>
              <input type="number" name="<?php echo esc_attr($key); ?>[smooth_offset]" value="<?php echo esc_attr($o['smooth_offset']); ?>" style="width:100px">
              <p class="description">Nhập chiều cao header cố định nếu muốn trừ khi scroll.</p>
            </td>
          </tr>

          <tr><th colspan="2"><h2>Assets</h2></th></tr>
          <tr>
            <th scope="row">Nạp CSS/JS của plugin</th>
            <td><label><input type="checkbox" name="<?php echo esc_attr($key); ?>[enqueue_assets]" value="1" <?php checked($o['enqueue_assets'],1); ?>> Enable</label>
              <p class="description">Tắt nếu bạn muốn dùng CSS/JS của theme.</p>
            </td>
          </tr>
        </table>
        <?php submit_button(); ?>
      </form>
    </div>
    <?php
  }

  /* ==================== FRONTEND ==================== */

  public function maybe_enqueue_assets() {
    $o = self::opts();
    if (!$o['enqueue_assets']) return;

    // CSS
    $css = "
      .rtotoc-wrap { margin:1rem 0 1.25rem; padding:12px 14px; border:1px solid #e5e7eb; border-radius:8px; background:#fafafa; }
      .rtotoc-title { margin:0 0 .5rem; font-weight:700; font-size:16px; }
      .rtotoc-list { margin:0; padding-left:18px; }
      .rtotoc-list li { margin:.25rem 0; }
      .rtotoc-reading { margin:0 0 12px; font-size:14px; color:#444; }
      .rtotoc-anchor-highlight { outline: 2px dashed rgba(244,210,79,.7); outline-offset: 4px; transition: outline-color .3s; }
      .rtotoc-link-active > a { text-decoration: underline; }
    ";
    wp_register_style('rtotoc-style', false, [], '1.0.0');
    wp_enqueue_style('rtotoc-style');
    wp_add_inline_style('rtotoc-style',$css);

    // JS
    wp_register_script('rtotoc-js', false, [], '1.0.0', true);
    wp_enqueue_script('rtotoc-js');

    $data = [
      'smoothOffset' => intval($o['smooth_offset']),
    ];
   $js = "
(function(){
  /* ===== EASING SCROLL (mượt) ===== */
  function easeInOutCubic(t){ return t<0.5 ? 4*t*t*t : 1 - Math.pow(-2*t+2,3)/2; }
  function animateScrollTo(targetY, duration){
    var start = window.pageYOffset || document.documentElement.scrollTop;
    var dist  = targetY - start;
    var t0 = null;
    function step(ts){
      if(!t0) t0 = ts;
      var p = Math.min((ts - t0)/duration, 1);
      var y = start + dist * easeInOutCubic(p);
      window.scrollTo(0, y);
      if(p < 1) requestAnimationFrame(step);
    }
    requestAnimationFrame(step);
  }

  function getHashIdFromLink(a){
    try{
      var href = a.getAttribute('href')||''; if(!href) return null;
      if(href[0]==='#') return decodeURIComponent(href.slice(1));
      var u = new URL(href, window.location.href);
      if(u.origin===location.origin && u.pathname===location.pathname && u.hash){
        return decodeURIComponent(u.hash.slice(1));
      }
      return null;
    }catch(e){ return null; }
  }

  var cfg = " . wp_json_encode([
    'smoothOffset' => (int) self::opts()['smooth_offset'],
    'levels'       => array_values(self::opts()['heading_levels']),
  ]) . ";

  var ACTIVATED_CLASS = 'rtoc-activated';
  var wrap = null, liMap = {}, headings = [], offset = parseInt(cfg.smoothOffset||0,10)||0;

  function activateTOC(){
    wrap = wrap || document.querySelector('.rtotoc-wrap');
    if (wrap && !wrap.classList.contains(ACTIVATED_CLASS)) wrap.classList.add(ACTIVATED_CLASS);
  }

  /* ===== Chuẩn bị map link<->heading ===== */
  function buildMaps(){
    liMap = {};
    document.querySelectorAll('.rtotoc-list a').forEach(function(a){
      var id = getHashIdFromLink(a);
      if(id) liMap[id] = a.parentElement;
    });

    var selector = (cfg.levels && cfg.levels.length) ? cfg.levels.join(',') : 'h2,h3';
    headings = Array.from(document.querySelectorAll(selector));
    // Fallback: nếu heading thiếu id, gán theo thứ tự của TOC
    var used = new Set();
    Object.keys(liMap).forEach(function(id){
      if(document.getElementById(id)) return;
      for(var i=0;i<headings.length;i++){
        if(used.has(i)) continue;
        var h = headings[i];
        if(!h.id){ h.id = id; used.add(i); break; }
      }
    });
    // Chỉ giữ những heading thực sự có id
    headings = headings.filter(function(h){ return !!h.id; });
  }

  /* ===== Chỉ 1 mục active dựa trên vị trí cuộn ===== */
  function updateActive(){
    if(!headings.length) return;
    var y = (window.pageYOffset || document.documentElement.scrollTop) + offset + 4;
    var currentId = null;

    for (var i=0; i<headings.length; i++){
      var hTop = headings[i].getBoundingClientRect().top + window.pageYOffset;
      if (hTop <= y) currentId = headings[i].id; else break;
    }
    if(!currentId) currentId = headings[0].id; // ở đầu trang

    // Clear tất cả rồi đặt active đúng 1 mục
    Object.keys(liMap).forEach(function(id){
      var li = liMap[id]; if(li) li.classList.remove('rtotoc-link-active');
    });
    var activeLi = liMap[currentId];
    if(activeLi) activeLi.classList.add('rtotoc-link-active');
  }

  /* ===== Click TOC: cuộn mượt + bật chế độ activated ===== */
  document.addEventListener('click', function(e){
    var a = e.target.closest('a'); if(!a) return;
    var id = getHashIdFromLink(a); if(!id) return;
    var el = document.getElementById(id); if(!el) return;

    e.preventDefault();              // ngăn nhảy mặc định
    activateTOC();

    var target = el.getBoundingClientRect().top + window.pageYOffset - offset;
    if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
      window.scrollTo(0, target);
    } else {
      animateScrollTo(target, 800);  // 600–1000ms tùy ý
    }
    history.pushState(null, '', '#'+id);
  }, true); // capture=true để không bị script khác chặn

  /* ===== Kích hoạt sau khi user cuộn tay để hiện underline đúng mục ===== */
  var activatedOnScroll = false;
  window.addEventListener('scroll', function(){
    if(!activatedOnScroll && (window.pageYOffset||document.documentElement.scrollTop) > 30){
      activatedOnScroll = true; activateTOC();
    }
    updateActive();
  }, {passive:true});

  document.addEventListener('DOMContentLoaded', function(){
    buildMaps();
    if (location.hash && document.getElementById(location.hash.slice(1))) activateTOC();
    updateActive();
    // cập nhật lại sau 300ms (khi ảnh/đoạn trên load xong thay đổi layout)
    setTimeout(updateActive, 300);
  });
})();
";


    wp_add_inline_script('rtotoc-js', $js);
  }

  public function maybe_inject_into_content($content) {
    if (!is_singular() || !in_the_loop() || !is_main_query()) return $content;

    $o = self::opts();
    if (!$o['enable_reading_time'] && !$o['enable_toc']) return $content;

    // Tạo HTML phần đầu (reading time +/or toc)
    $pieces = $this->build_pieces($content, get_post());

    // Nếu chọn manual thì không auto-inject, nhưng vẫn trả lại content gốc
    if ($o['toc_position'] === 'manual') {
      // Nếu muốn vẫn hiển thị Reading Time ở đầu mà TOC manual:
      // bạn có thể bật dòng dưới để prepend reading time:
      // return $pieces['reading_html'] . $content;
      return $content;
    }

    // Inject theo vị trí
    if ($o['toc_position'] === 'before') {
      return $pieces['wrap_html'].$content;
    } else { // after
      return $content.$pieces['wrap_html'];
    }
  }

  /* ==================== CORE BUILDERS ==================== */

  private function build_pieces($content, $post) {
    $o = self::opts();
    $reading_html = '';
    $toc_html = '';

    // Reading time
    if ($o['enable_reading_time']) {
      $wpm = max(50, (int)$o['wpm']);
      $text = wp_strip_all_tags($content);
      $words = str_word_count($text);
      $mins = max(1, ceil($words / $wpm));
      $label = esc_html($o['prepend_label']);
      $icon  = esc_html($o['icon_clock']);
      $reading_html = sprintf('<div class="rtotoc-reading">%s %s %d min</div>', $icon, $label, $mins);
    }

    // TOC
    if ($o['enable_toc']) {
      $toc_html = $this->generate_toc($content, $o['heading_levels'], (int)$o['min_headings'], $o['toc_title']);
    }

    $wrap_html = '';
    if ($reading_html || $toc_html) {
      $wrap_html = '<div class="rtotoc-wrap">';
      if ($reading_html) $wrap_html .= $reading_html;
      if ($toc_html)     $wrap_html .= $toc_html;
      $wrap_html .= '</div>';
    }

    return [
      'reading_html' => $reading_html,
      'toc_html'     => $toc_html,
      'wrap_html'    => $wrap_html,
    ];
  }

  /**
   * Tạo TOC và đồng thời gắn id vào các heading trong content (nếu chưa có).
   * Trả về HTML TOC (danh sách UL/LI) hoặc chuỗi rỗng nếu không đủ heading.
   */
  private function generate_toc(&$content, $levels, $min_headings, $title) {
    // Dùng DOMDocument để parse
    if (trim($content) === '') return '';

    libxml_use_internal_errors(true);
    $doc = new DOMDocument();
    // Gói content trong một div để loadFragment an toàn
    $html = '<div id="rtotoc-root">'.$content.'</div>';
    $doc->loadHTML('<?xml encoding="utf-8" ?>'.$html, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);

    $xpath = new DOMXPath($doc);
    $query = implode('|', array_map(function($l){ return "//".$l; }, $levels));
    $nodes = $xpath->query($query);

    if (!$nodes || $nodes->length < $min_headings) {
      // Không đủ heading → không tạo TOC, nhưng vẫn đảm bảo content giữ nguyên
      $content = $this->innerHTML($doc->getElementById('rtotoc-root'));
      return '';
    }

    $items = [];
    $index = 1;
    foreach ($nodes as $node) {
      $text = trim($node->textContent);
      if ($text === '') continue;

      // Gắn id nếu chưa có
      $id = '';
      if ($node->hasAttribute('id')) {
        $id = $node->getAttribute('id');
      } else {
        $id = 'rtoc-' . $index . '-' . sanitize_title(substr($text, 0, 60));
        $node->setAttribute('id', $id);
      }

      $items[] = [
        'level' => strtolower($node->nodeName),
        'id'    => $id,
        'text'  => $text,
      ];
      $index++;
    }

    // Cập nhật content (đã chèn id)
    $content = $this->innerHTML($doc->getElementById('rtotoc-root'));

    // Render TOC (đơn giản: 1 cấp; nâng cấp: lồng theo level)
    $html  = '<div class="rtotoc-toc">';
    $html .= '<div class="rtotoc-title">'.esc_html($title).'</div>';
    $html .= '<ul class="rtotoc-list">';
    foreach ($items as $it) {
      $html .= '<li class="rtotoc-item rt-'.$it['level'].'"><a href="#'.esc_attr($it['id']).'">'.esc_html($it['text']).'</a></li>';
    }
    $html .= '</ul></div>';

    return $html;
  }

  private function innerHTML(DOMNode $element) {
    $innerHTML = "";
    foreach ($element->childNodes as $child) {
      $innerHTML .= $element->ownerDocument->saveHTML($child);
    }
    return $innerHTML;
  }
}

/* Bootstrap */
add_action('plugins_loaded', function(){
  $GLOBALS['rtotoc'] = new RTOTOC();
});

/* ==================== TEMPLATE TAGS ==================== */
/**
 * In ra cả Reading Time + TOC (dùng cho chế độ manual hoặc cần tự chèn trong theme)
 *  - $args hiện tại không cần, để dành mở rộng sau
 */
function rtoc_display($args = []) {
  if (!is_singular()) return;
  $post = get_post();
  if (!$post) return;

  $o = RTOTOC::opts();
  if (!$o['enable_reading_time'] && !$o['enable_toc']) return;

  // Lấy content gốc (không qua filter tự chèn để tránh double)
  $content = $post->post_content;

  // Dùng cùng builder logic của class
  $rt = $GLOBALS['rtotoc'] ?? null;
  if (!$rt) return;

  $pieces = (new ReflectionClass($rt))->getMethod('build_pieces');
  $pieces->setAccessible(true);
  $res = $pieces->invoke($rt, $content, $post);

  echo $res['wrap_html']; // in ra block tổng hợp
}
