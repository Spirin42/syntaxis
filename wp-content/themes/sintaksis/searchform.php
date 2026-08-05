<?php if (!defined('ABSPATH')) exit; ?>
<form class="searchform" role="search" method="get" action="<?php echo esc_url(home_url('/')); ?>">
  <label class="searchform__label" for="sx-search">Поиск по сайту</label>
  <input class="searchform__field" type="search" id="sx-search" name="s" value="<?php echo esc_attr(get_search_query()); ?>" placeholder="Название, автор, слово из текста">
  <button class="searchform__go" type="submit">Найти</button>
</form>
