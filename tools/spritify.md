# Spritify #46a97#

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

# Changelog


* #ctv


* 2024-01-19 16:04:51 46a97
  * Now supports several arrange patterns: Square, Line, Column
  * File mask now can be array of glob masks
  * Code refactoring


* 46a89
  * Error handling
  * Streamlining sources
* 46a88
  * Now support glob masks (like in OS - with `*` and `?`) with `GLOB_BRACE` extension
