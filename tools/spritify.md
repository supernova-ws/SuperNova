# Spritify #46a98#

Tool that make sprites from set of images

Will arrange all images into predefined patterns. Patterns supported:
* Square
* Line
* Column

Supports scaling (via `zoom` CSS property) to predefined box (square only)

# Todo

* Layouts: btree, same_height, same_width, pixel-size
* Сортировать изображения еще и по полному пути/имени изображения/прификсу имени?
  * menu_item -> menu_item_hidden -> menu_item_highlighted
* Code
  * Отдельный класс/метод построения спрайта 
  * Картинки должен хранить спрайт
    * Позиции в спрайте хранятся в image
  * Не линии - а имеджсет, который может быть и линией и... не-линией?

* CSS animations from animated gif
  * JS animated sprites https://jsfiddle.net/Camilo/n3xnn/
  * CSS sprite animation https://jsfiddle.net/simurai/CGmCe/
  * Animated GIF PHP encoder/decoder https://github.com/stil/gif-endec
    * https://itecnote.com/tecnote/php-detect-animated-gifs-using-php-and-gd/
  * file:///X:/Documents/Projects/supernova/supernova_trunk/tools/_test/border.html

# Changelog

2024-01-19 16:04:51 is Fri Jan 19 18:04:55 2024 +0200

* #ctv


* 2024-01-19 17:51:35 46a98
  * Now `SpriteLine` aware of line limitations and also can control it's filling


* 2024-01-19 16:04:51 46a97
  * Now supports several arrange patterns: Square, Line, Column
  * File mask now can be array of glob masks
  * Code refactoring


* 2024-01-14 09:53:29 46a89
  * Error handling
  * Streamlining sources


* 2024-01-14 06:52:51 46a88
  * Now support glob masks (like in OS - with `*` and `?`) with `GLOB_BRACE` extension
