# Spritify #46a104#

Tool that make sprites from set of images - PNG sprite file along with CSS to use

Will arrange all images into predefined layout pattern.

* Patterns supported:
    * Square
    * Line
    * Column
* Layout pattern ignored for animated GIFs - each GIF decompresses in it's own line of images

Supports scaling (via `zoom` CSS property) to predefined box (square only)

Basic support for animated GIFs:

* Extract all frames in one "line" (layout pattern ignored for animated GIFs)
* Expand each sprite to fully-qualified image (SpriteLineGif::$expandFrame === true)
    * Only `DO_NOT_DISPOSE`, `UNSPECIFIED` and `RESTORE_TO_BACKGROUND_COLOR` disposal methods supported for now
* Generates CSS per frame with extra info: frame position and size along with disposition method
* Generates pure CSS animations via `@keyframes` and `animation` property
    * Spritify honors A-GIF frame delays
    * Spritify honors A-GIF frame offsets

# Thanks

* https://github.com/stil/gif-endec - Animated GIF encoding/decoding lib
* Animated GIF detection - https://itecnote.com/tecnote/php-detect-animated-gifs-using-php-and-gd/
* CSS sprite animation - https://jsfiddle.net/simurai/CGmCe/

# Todo

* Layouts: btree, same_height, same_width, pixel-size
* Сортировать изображения еще и по полному пути/имени изображения/префиксу имени?
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
    * https://www.youtube.com/watch?v=jLbz6LRblV0
        * https://matthewrayfield.com/articles/encoding-animated-gifs-into-pure-css/
        * https://matthewrayfield.com/projects/gif2css/
        * https://github.com/MatthewRayfield/gif2css
    * https://www.w3.org/Graphics/GIF/spec-gif89a.txt

# WiP

* GIF decoding
    * ? Background color on disposition `RESTORE_TO_BACKGROUND_COLOR`
    * ? Is it possible then size + offset be larger then largest frame? Read spec or find GIF
    * Streamline code for files to use GdImageWrapper as base image class and convert frames to it

# Changelog

2024-01-19 16:04:51 is Fri Jan 19 18:04:55 2024 +0200

* #ctv


* 2024-01-21 07:04:38 46a104
    * Adjusted behavior in case if first frame in A-GIF smaller then largest one


* 2024-01-21 06:17:01 46a103
    * Now Spritify honors offset in first frame of A-GIF


* 2024-01-21 04:44:55 46a102
    * Spritify supports `RESTORE_TO_BACKGROUND_COLOR` disposition method


* 2024-01-21 03:55:30 46a101
    * Now Spritify generates pure CSS animations for extracted A-GIF frames, honoring delay between frames


* 2024-01-21 02:45:25 46a100
    * Now Spritify expand each extracted A-GIF partial frame to fully rendered one
    * Only disposal methods `DO_NOT_DISPOSE` and `UNSPECIFIED` supported


* 2024-01-21 01:27:46 46a99
    * Now Spritify can decompress animated GIF to set of frames


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
