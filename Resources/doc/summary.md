Summary
=======

## Class overview

#### Template

This entity contains necessary information to be able to construct a document. It has the base or master view file, 
as well as some optional blocks as content.

#### Block

Content in web pages are build using blocks. The Block is the model in an MVC-like structure where View is a twig
file that the Controller BlockService will reference.

#### BlockService

This class works like a controller for the block and simultaneously expose methods to provide FormTypes for content
management purposes.

#### BlockContainer

The BlockContainerInterface is used to identify classes that can hold Blocks as content. Usually these classes are
directly addressable and viewable like Pages.


