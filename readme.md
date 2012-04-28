# CLImate.php - Your mate for PHP CLI apps.

### Early development stage

[![Build Status](https://secure.travis-ci.org/smasty/CLImate.php.png?branch=master)](http://travis-ci.org/smasty/CLImate.php)

## Implemented features

### Input/Output

Reading from standard input, as well as writing to standard output and error.
Methods for prompting for questions, choosing from choices `[y/n/?]` and menus.
Support for rendering tables (with sorting, custom row formats, etc).

### Argument handling (draft)

Class for parsing all types of command-line arguments.

### Notifiers

Various notifiers:

- Spinner - `-\|/`
- Dots - `Loading...`
- Progress bar - `[===>   ]`


## Yet to be implemented

- Colorized output
- Finish argument handling
- Command-line applications infrastructure


## The MIT License


Copyright (c) 2012 Smasty, http://smasty.net/

Permission is hereby granted, free of charge, to any person obtaining
a copy of this software and associated documentation files (the
"Software"), to deal in the Software without restriction, including
without limitation the rights to use, copy, modify, merge, publish,
distribute, sublicense, and/or sell copies of the Software, and to
permit persons to whom the Software is furnished to do so, subject to
the following conditions:

The above copyright notice and this permission notice shall be
included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF
MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND
NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE
LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION
OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION
WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.