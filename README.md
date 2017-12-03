# zenReturnManager_BETA
Zen Return Manager as Return and Cancel manager for Zen Cart 1.5.5e

==============
This is a beta test of a Return/Cancel management Module for Zen Cart 1.5.5e.  It's offered here for anyone interested in what I've done on my own site where it's in full use. This version is supported in the ZenCart Forum, use the github contact address if you need help, have questions or comments or jump to the forum.

This version is not an update to anything and needs to be installed as it's own mod.

I have only tested and designed this with ZC 1.5.5e and am not planning to down grade the code. The template used was the default responsive classic template that comes with ZC 1.5.5e and based on sound responsive design technique.

ABOUT Return Manager
=====================
This module lets my customers check the status, cancel or start the return process without having an account or logging in to one, based on COWAA. It's difficult to run a store efficiently without a mechanism for accepting, tracking, and processing returns.

Installation Instructions? Most likely not to spec's... coders, web designers should not have any problems.. Unmodified ZC 1.5.5e can drag and drop, the only core file edits is in the admin orders.php file and are well marked.

Return Manager three basic ideas:
 * Customer Cancel ordered items - Store side login not required. Customer enters order number and email address for this order. If items have an order status below SHIPPED, then Cancellation is offered per-item!
 * Customer Return ordered items - Store side login not required. Customer enters order number and email address for this order. If items have an order status as SHIPPED or DELIVERED and within your return set days, then Return is offered per-item! An RMA number is issued for returns.
 * Admin Return/Cancel - Button in orders will allow admin to create an return or cancel for any item within this order.

===========================
closing note:
1) Installation requires some skills. Drag and drop would work if admin orders.php was never modified and this is a vanilla ZC1.5.5e install! Backing up database and site is highly suggested. We can fix bugs, but not bad install. 


