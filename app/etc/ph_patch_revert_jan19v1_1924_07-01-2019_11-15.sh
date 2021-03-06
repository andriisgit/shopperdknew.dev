#!/bin/bash
# This file reverts the bakup applied by Powerhosting 07-01-2019_11-15
initdir=$(pwd)
cd "/var/www/hifi-freaks.se/public_html"
REVERTTIME=$(date +%d-%m-%Y_%H-%M)
correct_owner=$(stat -c "%U:%G" index.php)
revertmail=support@hififreaks.dk
jscsslist_bu=$(tar -tf app/etc/ph_patch_backup_jan19v1_1924_07-01-2019_11-15.tar.gz | grep "\.js\|\.css" | cut -d/ -f2-)

rm ./app/code/core/Mage/Admin/etc/config.xml
rm ./app/code/core/Mage/Admin/Helper/Block.php
rm ./app/code/core/Mage/Adminhtml/Block/Catalog/Product/Composite/Fieldset/Options.php
rm ./app/code/core/Mage/Adminhtml/Block/Catalog/Product/Edit/Tab/Options/Option.php
rm ./app/code/core/Mage/Adminhtml/Block/Catalog/Product/Edit/Tab/Super/Config.php
rm ./app/code/core/Mage/Adminhtml/Block/Catalog/Product/Grid.php
rm ./app/code/core/Mage/Adminhtml/Block/Catalog/Product/Helper/Form/Gallery/Content.php
rm ./app/code/core/Mage/Adminhtml/Block/Checkout/Formkey.php
rm ./app/code/core/Mage/Adminhtml/Block/Cms/Wysiwyg/Images/Content/Uploader.php
rm ./app/code/core/Mage/Adminhtml/Block/Customer/Group/Edit.php
rm ./app/code/core/Mage/Adminhtml/Block/Media/Uploader.php
rm ./app/code/core/Mage/Adminhtml/Block/Newsletter/Template/Edit.php
rm ./app/code/core/Mage/Adminhtml/Block/Newsletter/Template/Grid/Renderer/Sender.php
rm ./app/code/core/Mage/Adminhtml/Block/Notification/Grid/Renderer/Notice.php
rm ./app/code/core/Mage/Adminhtml/Block/Notification/Symlink.php
rm ./app/code/core/Mage/Adminhtml/Block/Report/Review/Detail.php
rm ./app/code/core/Mage/Adminhtml/Block/Report/Tag/Product/Detail.php
rm ./app/code/core/Mage/Adminhtml/Block/Review/Add.php
rm ./app/code/core/Mage/Adminhtml/Block/Review/Edit/Form.php
rm ./app/code/core/Mage/Adminhtml/Block/Sales/Order/Grid.php
rm ./app/code/core/Mage/Adminhtml/Block/Sales/Order/View/Info.php
rm ./app/code/core/Mage/Adminhtml/Block/System/Store/Edit/Form.php
rm ./app/code/core/Mage/Adminhtml/Block/Tag/Assigned/Grid.php
rm ./app/code/core/Mage/Adminhtml/Block/Urlrewrite/Category/Tree.php
rm ./app/code/core/Mage/Adminhtml/Block/Widget/Form/Container.php
rm ./app/code/core/Mage/Adminhtml/Block/Widget/Grid/Column/Filter/Date.php
rm ./app/code/core/Mage/Adminhtml/Block/Widget/Grid/Column/Filter/Datetime.php
rm ./app/code/core/Mage/Adminhtml/Block/Widget/Grid/Column/Renderer/Store.php
rm ./app/code/core/Mage/Adminhtml/Block/Widget/Grid/Massaction/Abstract.php
rm ./app/code/core/Mage/Adminhtml/Block/Widget/Tabs.php
rm ./app/code/core/Mage/Adminhtml/Controller/Action.php
rm ./app/code/core/Mage/Adminhtml/controllers/Catalog/CategoryController.php
rm ./app/code/core/Mage/Adminhtml/controllers/Catalog/ProductController.php
rm ./app/code/core/Mage/Adminhtml/controllers/Catalog/Product/GalleryController.php
rm ./app/code/core/Mage/Adminhtml/controllers/Cms/BlockController.php
rm ./app/code/core/Mage/Adminhtml/controllers/Cms/WysiwygController.php
rm ./app/code/core/Mage/Adminhtml/controllers/Cms/Wysiwyg/ImagesController.php
rm ./app/code/core/Mage/Adminhtml/controllers/CustomerController.php
rm ./app/code/core/Mage/Adminhtml/controllers/Customer/GroupController.php
rm ./app/code/core/Mage/Adminhtml/controllers/DashboardController.php
rm ./app/code/core/Mage/Adminhtml/controllers/IndexController.php
rm ./app/code/core/Mage/Adminhtml/controllers/Media/UploaderController.php
rm ./app/code/core/Mage/Adminhtml/controllers/Newsletter/QueueController.php
rm ./app/code/core/Mage/Adminhtml/controllers/Newsletter/TemplateController.php
rm ./app/code/core/Mage/Adminhtml/controllers/Permissions/UserController.php
rm ./app/code/core/Mage/Adminhtml/controllers/SitemapController.php
rm ./app/code/core/Mage/Adminhtml/controllers/System/BackupController.php
rm ./app/code/core/Mage/Adminhtml/controllers/System/StoreController.php
rm ./app/code/core/Mage/Adminhtml/etc/config.xml
rm ./app/code/core/Mage/Adminhtml/Model/Config/Data.php
rm ./app/code/core/Mage/Adminhtml/Model/LayoutUpdate/Validator.php
rm ./app/code/core/Mage/Adminhtml/Model/System/Config/Backend/Filename.php
rm ./app/code/core/Mage/Adminhtml/Model/System/Config/Backend/Serialized.php
rm ./app/code/core/Mage/Adminhtml/Model/System/Config/Backend/Symlink.php
rm ./app/code/core/Mage/Adminhtml/Model/System/Store.php
rm ./app/code/core/Mage/Admin/Model/Block.php
rm ./app/code/core/Mage/Admin/Model/Resource/Block.php
rm ./app/code/core/Mage/Admin/Model/Session.php
rm ./app/code/core/Mage/Admin/Model/User.php
rm ./app/code/core/Mage/Api/Helper/Data.php
rm ./app/code/core/Mage/Api/Model/Server/Adapter/Soap.php
rm ./app/code/core/Mage/Api/Model/Wsdl/Config/Base.php
rm ./app/code/core/Mage/Api/Model/Wsdl/Config.php
rm ./app/code/core/Mage/Captcha/etc/config.xml
rm ./app/code/core/Mage/Captcha/Model/Observer.php
rm ./app/code/core/Mage/Captcha/Model/Zend.php
rm ./app/code/core/Mage/Catalog/etc/config.xml
rm ./app/code/core/Mage/Catalog/etc/system.xml
rm ./app/code/core/Mage/Catalog/Helper/Image.php
rm ./app/code/core/Mage/Catalog/Model/Api2/Product/Image/Rest/Admin/V1.php
rm ./app/code/core/Mage/Catalog/Model/Product/Api/V2.php
rm ./app/code/core/Mage/Catalog/Model/Product/Attribute/Media/Api.php
rm ./app/code/core/Mage/Catalog/Model/Product.php
rm ./app/code/core/Mage/Catalog/Model/Resource/Category/Tree.php
rm ./app/code/core/Mage/Centinel/Model/Api/Client.php
rm ./app/code/core/Mage/Centinel/Model/Api.php
rm ./app/code/core/Mage/Checkout/controllers/CartController.php
rm ./app/code/core/Mage/Checkout/controllers/MultishippingController.php
rm ./app/code/core/Mage/Checkout/controllers/OnepageController.php
rm ./app/code/core/Mage/Checkout/controllers/OnepageController.php.orig
rm ./app/code/core/Mage/Checkout/etc/system.xml
rm ./app/code/core/Mage/Checkout/Model/Api/Resource/Customer.php
rm ./app/code/core/Mage/Checkout/Model/Type/Onepage.php
rm ./app/code/core/Mage/Cms/Model/Wysiwyg/Images/Storage.php
rm ./app/code/core/Mage/Cms/Model/Wysiwyg/Images/Storage.php.orig
rm ./app/code/core/Mage/Core/Block/Abstract.php
rm ./app/code/core/Mage/Core/Controller/Front/Action.php
rm ./app/code/core/Mage/Core/Controller/Request/Http.php
rm ./app/code/core/Mage/Core/etc/config.xml
rm ./app/code/core/Mage/Core/etc/system.xml
rm ./app/code/core/Mage/Core/functions.php
rm ./app/code/core/Mage/Core/Helper/Http.php
rm ./app/code/core/Mage/Core/Helper/String.php
rm ./app/code/core/Mage/Core/Helper/Url.php
rm ./app/code/core/Mage/Core/Model/Config.php
rm ./app/code/core/Mage/Core/Model/Email/Template/Abstract.php
rm ./app/code/core/Mage/Core/Model/Encryption.php
rm ./app/code/core/Mage/Core/Model/File/Validator/Image.php
rm ./app/code/core/Mage/Core/Model/Input/Filter/MaliciousCode.php
rm ./app/code/core/Mage/Core/Model/Session/Abstract/Varien.php
rm ./app/code/core/Mage/Core/Model/Variable.php
rm ./app/code/core/Mage/Core/sql/core_setup/upgrade-1.6.0.6.1.1-1.6.0.6.1.2.php
rm ./app/code/core/Mage/Core/sql/core_setup/upgrade-1.6.0.6.1.2-1.6.0.6.1.3.php
rm ./app/code/core/Mage/Customer/Block/Address/Book.php
rm ./app/code/core/Mage/Customer/controllers/AccountController.php
rm ./app/code/core/Mage/Customer/controllers/AddressController.php
rm ./app/code/core/Mage/Customer/etc/config.xml
rm ./app/code/core/Mage/Customer/Helper/Data.php
rm ./app/code/core/Mage/Customer/Model/Customer.php
rm ./app/code/core/Mage/Customer/Model/Resource/Customer.php
rm ./app/code/core/Mage/Customer/Model/Session.php
rm ./app/code/core/Mage/Customer/sql/customer_setup/upgrade-1.6.2.0.4.1.1-1.6.2.0.4.1.2.php
rm ./app/code/core/Mage/Customer/sql/customer_setup/upgrade-1.6.2.0.4.1.2-1.6.2.0.4.1.3.php
rm ./app/code/core/Mage/Dataflow/Model/Convert/Adapter/Zend/Cache.php
rm ./app/code/core/Mage/Dataflow/Model/Convert/Container/Abstract.php
rm ./app/code/core/Mage/Dataflow/Model/Convert/Parser/Csv.php
rm ./app/code/core/Mage/Dataflow/Model/Convert/Parser/Xml/Excel.php
rm ./app/code/core/Mage/Dataflow/Model/Profile.php
rm ./app/code/core/Mage/Downloadable/Block/Adminhtml/Catalog/Product/Edit/Tab/Downloadable/Links.php
rm ./app/code/core/Mage/Downloadable/Block/Adminhtml/Catalog/Product/Edit/Tab/Downloadable/Samples.php
rm ./app/code/core/Mage/Downloadable/etc/config.xml
rm ./app/code/core/Mage/Downloadable/etc/system.xml
rm ./app/code/core/Mage/Downloadable/Helper/File.php
rm ./app/code/core/Mage/Downloadable/sql/downloadable_setup/upgrade-1.6.0.0.2.1.1-1.6.0.0.2.1.2.php
rm ./app/code/core/Mage/Eav/Model/Entity/Attribute/Backend/Serialized.php
rm ./app/code/core/Mage/ImportExport/Model/Import/Entity/Customer/Address.php
rm ./app/code/core/Mage/ImportExport/Model/Import/Entity/Customer.php
rm ./app/code/core/Mage/ImportExport/Model/Import/Entity/Product.php
rm ./app/code/core/Mage/ImportExport/Model/Import.php
rm ./app/code/core/Mage/ImportExport/Model/Import/Uploader.php
rm ./app/code/core/Mage/Log/Helper/Data.php
rm ./app/code/core/Mage/Log/Model/Visitor.php
rm ./app/code/core/Mage/Oauth/Model/Server.php
rm ./app/code/core/Mage/Paygate/Model/Authorizenet.php
rm ./app/code/core/Mage/Payment/Block/Info/Checkmo.php
rm ./app/code/core/Mage/Payment/etc/config.xml
rm ./app/code/core/Mage/Payment/etc/system.xml
rm ./app/code/core/Mage/Payment/sql/payment_setup/upgrade-1.6.0.0.1.1-1.6.0.0.1.2.php
rm ./app/code/core/Mage/Paypal/Model/Express/Checkout.php
rm ./app/code/core/Mage/Paypal/Model/Resource/Payment/Transaction.php
rm ./app/code/core/Mage/Rss/Helper/Data.php
rm ./app/code/core/Mage/Rule/Model/Abstract.php
rm ./app/code/core/Mage/Sales/Block/Adminhtml/Billing/Agreement/Grid.php
rm ./app/code/core/Mage/Sales/Model/Quote/Item.php
rm ./app/code/core/Mage/Sales/Model/Resource/Order/Item/Collection.php
rm ./app/code/core/Mage/Sales/Model/Resource/Order/Payment.php
rm ./app/code/core/Mage/Sales/Model/Resource/Order/Payment/Transaction.php
rm ./app/code/core/Mage/Sales/Model/Resource/Quote/Payment.php
rm ./app/code/core/Mage/Sales/Model/Resource/Recurring/Profile.php
rm ./app/code/core/Mage/Sendfriend/Block/Send.php
rm ./app/code/core/Mage/Shipping/Model/Info.php
rm ./app/code/core/Mage/Uploader/Block/Abstract.php
rm ./app/code/core/Mage/Uploader/Block/Multiple.php
rm ./app/code/core/Mage/Uploader/Block/Single.php
rm ./app/code/core/Mage/Uploader/etc/config.xml
rm ./app/code/core/Mage/Uploader/etc/jstranslator.xml
rm ./app/code/core/Mage/Uploader/Helper/Data.php
rm ./app/code/core/Mage/Uploader/Helper/File.php
rm ./app/code/core/Mage/Uploader/Model/Config/Abstract.php
rm ./app/code/core/Mage/Uploader/Model/Config/Browsebutton.php
rm ./app/code/core/Mage/Uploader/Model/Config/Misc.php
rm ./app/code/core/Mage/Uploader/Model/Config/Uploader.php
rm ./app/code/core/Mage/Usa/etc/config.xml
rm ./app/code/core/Mage/Usa/etc/system.xml
rm ./app/code/core/Mage/Usa/Helper/Data.php
rm ./app/code/core/Mage/Usa/Model/Shipping/Carrier/Abstract/Backend/Abstract.php
rm ./app/code/core/Mage/Usa/Model/Shipping/Carrier/Dhl/International.php
rm ./app/code/core/Mage/Usa/Model/Shipping/Carrier/Dhl.php
rm ./app/code/core/Mage/Usa/Model/Shipping/Carrier/Fedex.php
rm ./app/code/core/Mage/Usa/Model/Shipping/Carrier/Ups/Backend/Freemethod.php
rm ./app/code/core/Mage/Usa/Model/Shipping/Carrier/Ups/Backend/OriginShipment.php
rm ./app/code/core/Mage/Usa/Model/Shipping/Carrier/Ups/Backend/Type.php
rm ./app/code/core/Mage/Usa/Model/Shipping/Carrier/Ups.php
rm ./app/code/core/Mage/Widget/controllers/Adminhtml/Widget/InstanceController.php
rm ./app/code/core/Mage/Widget/Model/Widget/Instance.php
rm ./app/code/core/Mage/Wishlist/controllers/IndexController.php
rm ./app/code/core/Mage/Wishlist/Helper/Data.php
rm ./app/code/core/Mage/XmlConnect/Block/Adminhtml/Connect/Dashboard/StoreSwitcher.php
rm ./app/code/core/Mage/XmlConnect/Block/Adminhtml/Mobile/Edit/Tab/Design/Images.php
rm ./app/code/core/Mage/XmlConnect/Block/Adminhtml/Mobile/Edit/Tab/Design.php
rm ./app/code/core/Mage/XmlConnect/controllers/Adminhtml/MobileController.php
rm ./app/code/core/Mage/XmlConnect/controllers/ReviewController.php
rm ./app/code/core/Mage/XmlConnect/Helper/Image.php
rm ./app/code/core/Zend/Controller/Request/Http.php
rm ./app/code/core/Zend/Filter/PregReplace.php
rm ./app/code/core/Zend/Form/Decorator/Form.php
rm ./app/code/core/Zend/Serializer/Adapter/PhpCode.php
rm ./app/code/core/Zend/Validate/EmailAddress.php
rm ./app/design/adminhtml/default/default/layout/cms.xml
rm ./app/design/adminhtml/default/default/layout/main.xml
rm ./app/design/adminhtml/default/default/layout/xmlconnect.xml
rm ./app/design/adminhtml/default/default/template/bundle/product/edit/bundle/option.phtml
rm ./app/design/adminhtml/default/default/template/bundle/sales/creditmemo/create/items/renderer.phtml
rm ./app/design/adminhtml/default/default/template/bundle/sales/creditmemo/view/items/renderer.phtml
rm ./app/design/adminhtml/default/default/template/bundle/sales/invoice/create/items/renderer.phtml
rm ./app/design/adminhtml/default/default/template/bundle/sales/invoice/view/items/renderer.phtml
rm ./app/design/adminhtml/default/default/template/bundle/sales/order/view/items/renderer.phtml
rm ./app/design/adminhtml/default/default/template/bundle/sales/shipment/create/items/renderer.phtml
rm ./app/design/adminhtml/default/default/template/bundle/sales/shipment/view/items/renderer.phtml
rm ./app/design/adminhtml/default/default/template/catalog/product/attribute/options.phtml
rm ./app/design/adminhtml/default/default/template/catalog/product/attribute/set/main.phtml
rm ./app/design/adminhtml/default/default/template/catalog/product/edit/options/type/file.phtml
rm ./app/design/adminhtml/default/default/template/catalog/product/helper/gallery.phtml
rm ./app/design/adminhtml/default/default/template/cms/browser/content/files.phtml
rm ./app/design/adminhtml/default/default/template/cms/browser/content/uploader.phtml
rm ./app/design/adminhtml/default/default/template/customer/tab/view.phtml
rm ./app/design/adminhtml/default/default/template/customer/tab/view/sales.phtml
rm ./app/design/adminhtml/default/default/template/dashboard/store/switcher.phtml
rm ./app/design/adminhtml/default/default/template/downloadable/product/composite/fieldset/downloadable.phtml
rm ./app/design/adminhtml/default/default/template/downloadable/product/edit/downloadable/links.phtml
rm ./app/design/adminhtml/default/default/template/downloadable/product/edit/downloadable.phtml
rm ./app/design/adminhtml/default/default/template/downloadable/product/edit/downloadable/samples.phtml
rm ./app/design/adminhtml/default/default/template/downloadable/sales/items/column/downloadable/creditmemo/name.phtml
rm ./app/design/adminhtml/default/default/template/downloadable/sales/items/column/downloadable/invoice/name.phtml
rm ./app/design/adminhtml/default/default/template/downloadable/sales/items/column/downloadable/name.phtml
rm ./app/design/adminhtml/default/default/template/eav/attribute/options.phtml
rm ./app/design/adminhtml/default/default/template/login.phtml
rm ./app/design/adminhtml/default/default/template/media/uploader.phtml
rm ./app/design/adminhtml/default/default/template/newsletter/preview/store.phtml
rm ./app/design/adminhtml/default/default/template/notification/formkey.phtml
rm ./app/design/adminhtml/default/default/template/notification/symlink.phtml
rm ./app/design/adminhtml/default/default/template/notification/toolbar.phtml
rm ./app/design/adminhtml/default/default/template/oauth/authorize/form/login.phtml
rm ./app/design/adminhtml/default/default/template/oauth/authorize/form/login-simple.phtml
rm ./app/design/adminhtml/default/default/template/oauth/authorize/head-simple.phtml
rm ./app/design/adminhtml/default/default/template/page/head.phtml
rm ./app/design/adminhtml/default/default/template/report/store/switcher.phtml
rm ./app/design/adminhtml/default/default/template/resetforgottenpassword.phtml
rm ./app/design/adminhtml/default/default/template/sales/billing/agreement/view/tab/info.phtml
rm ./app/design/adminhtml/default/default/template/sales/order/view/history.phtml
rm ./app/design/adminhtml/default/default/template/sales/order/view/info.phtml
rm ./app/design/adminhtml/default/default/template/store/switcher/enhanced.phtml
rm ./app/design/adminhtml/default/default/template/store/switcher.phtml
rm ./app/design/adminhtml/default/default/template/system/convert/profile/wizard.phtml
rm ./app/design/adminhtml/default/default/template/system/shipping/ups.phtml
rm ./app/design/adminhtml/default/default/template/tax/rate/title.phtml
rm ./app/design/adminhtml/default/default/template/widget/form/renderer/fieldset.phtml
rm ./app/design/adminhtml/default/default/template/xmlconnect/edit/tab/content.phtml
rm ./app/design/adminhtml/default/default/template/xmlconnect/edit/tab/design/image_edit.phtml
rm ./app/design/adminhtml/default/default/template/xmlconnect/edit/tab/design.phtml
rm ./app/design/frontend/base/default/layout/captcha.xml
rm ./app/design/frontend/base/default/template/bundle/email/order/items/creditmemo/default.phtml
rm ./app/design/frontend/base/default/template/bundle/email/order/items/invoice/default.phtml
rm ./app/design/frontend/base/default/template/bundle/email/order/items/order/default.phtml
rm ./app/design/frontend/base/default/template/bundle/email/order/items/shipment/default.phtml
rm ./app/design/frontend/base/default/template/bundle/sales/order/creditmemo/items/renderer.phtml
rm ./app/design/frontend/base/default/template/bundle/sales/order/invoice/items/renderer.phtml
rm ./app/design/frontend/base/default/template/bundle/sales/order/items/renderer.phtml
rm ./app/design/frontend/base/default/template/bundle/sales/order/shipment/items/renderer.phtml
rm ./app/design/frontend/base/default/template/checkout/cart/shipping.phtml
rm ./app/design/frontend/base/default/template/checkout/multishipping/addresses.phtml
rm ./app/design/frontend/base/default/template/checkout/multishipping/billing.phtml
rm ./app/design/frontend/base/default/template/checkout/multishipping/shipping.phtml
rm ./app/design/frontend/base/default/template/checkout/onepage/billing.phtml
rm ./app/design/frontend/base/default/template/checkout/onepage/payment.phtml
rm ./app/design/frontend/base/default/template/checkout/onepage/payment.phtml.orig
rm ./app/design/frontend/base/default/template/checkout/onepage/shipping_method.phtml
rm ./app/design/frontend/base/default/template/checkout/onepage/shipping.phtml
rm ./app/design/frontend/base/default/template/downloadable/catalog/product/links.phtml
rm ./app/design/frontend/base/default/template/downloadable/checkout/cart/item/default.phtml
rm ./app/design/frontend/base/default/template/downloadable/checkout/multishipping/item/downloadable.phtml
rm ./app/design/frontend/base/default/template/downloadable/checkout/onepage/review/item.phtml
rm ./app/design/frontend/base/default/template/downloadable/email/order/items/creditmemo/downloadable.phtml
rm ./app/design/frontend/base/default/template/downloadable/email/order/items/invoice/downloadable.phtml
rm ./app/design/frontend/base/default/template/downloadable/email/order/items/order/downloadable.phtml
rm ./app/design/frontend/base/default/template/downloadable/sales/order/creditmemo/items/renderer/downloadable.phtml
rm ./app/design/frontend/base/default/template/downloadable/sales/order/invoice/items/renderer/downloadable.phtml
rm ./app/design/frontend/base/default/template/downloadable/sales/order/items/renderer/downloadable.phtml
rm ./app/design/frontend/base/default/template/persistent/checkout/onepage/billing.phtml
rm ./app/design/frontend/base/default/template/wishlist/sharing.phtml
rm ./app/design/frontend/default/iphone/template/downloadable/checkout/cart/item/default.phtml
rm ./app/design/frontend/default/iphone/template/downloadable/checkout/onepage/review/item.phtml
rm ./app/design/frontend/default/iphone/template/downloadable/sales/order/creditmemo/items/renderer/downloadable.phtml
rm ./app/design/frontend/default/iphone/template/downloadable/sales/order/invoice/items/renderer/downloadable.phtml
rm ./app/design/frontend/default/iphone/template/downloadable/sales/order/items/renderer/downloadable.phtml
rm ./app/design/frontend/rwd/default/layout/page.xml
rm ./app/design/frontend/rwd/default/template/bundle/email/order/items/creditmemo/default.phtml
rm ./app/design/frontend/rwd/default/template/bundle/email/order/items/invoice/default.phtml
rm ./app/design/frontend/rwd/default/template/bundle/email/order/items/order/default.phtml
rm ./app/design/frontend/rwd/default/template/bundle/email/order/items/shipment/default.phtml
rm ./app/design/frontend/rwd/default/template/bundle/sales/order/items/renderer.phtml
rm ./app/design/frontend/rwd/default/template/checkout/cart/shipping.phtml
rm ./app/design/frontend/rwd/default/template/checkout/multishipping/addresses.phtml
rm ./app/design/frontend/rwd/default/template/checkout/multishipping/billing.phtml
rm ./app/design/frontend/rwd/default/template/checkout/onepage/payment.phtml
rm ./app/design/frontend/rwd/default/template/checkout/onepage/payment.phtml.orig
rm ./app/design/frontend/rwd/default/template/checkout/onepage/shipping.phtml
rm ./app/design/frontend/rwd/default/template/downloadable/checkout/cart/item/default.phtml
rm ./app/design/frontend/rwd/default/template/downloadable/checkout/onepage/review/item.phtml
rm ./app/design/frontend/rwd/default/template/downloadable/email/order/items/creditmemo/downloadable.phtml
rm ./app/design/frontend/rwd/default/template/downloadable/email/order/items/invoice/downloadable.phtml
rm ./app/design/frontend/rwd/default/template/downloadable/email/order/items/order/downloadable.phtml
rm ./app/design/frontend/rwd/default/template/downloadable/sales/order/items/renderer/downloadable.phtml
rm ./app/design/frontend/rwd/default/template/persistent/checkout/onepage/billing.phtml
rm ./app/design/frontend/rwd/default/template/sendfriend/send.phtml
rm ./app/design/install/default/default/template/install/create_admin.phtml
rm ./app/etc/applied.patches.list
rm ./app/etc/config.xml
rm ./app/etc/modules/Mage_All.xml
rm ./app/etc/modules/Mage_Captcha.xml
rm ./app/locale/en_US/Mage_Adminhtml.csv
rm ./app/locale/en_US/Mage_Adminhtml.csv.orig
rm ./app/locale/en_US/Mage_Catalog.csv
rm ./app/locale/en_US/Mage_Core.csv
rm ./app/locale/en_US/Mage_Core.csv.orig
rm ./app/locale/en_US/Mage_Customer.csv
rm ./app/locale/en_US/Mage_Dataflow.csv
rm ./app/locale/en_US/Mage_ImportExport.csv
rm ./app/locale/en_US/Mage_Media.csv
rm ./app/locale/en_US/Mage_Uploader.csv
rm ./app/locale/en_US/Mage_Usa.csv
rm ./app/locale/en_US/Mage_Wishlist.csv
rm ./app/locale/en_US/Mage_XmlConnect.csv
rm ./app/locale/en_US/template/email/account_password_reset_confirmation.html
rm ./app/locale/en_US/template/email/admin_new_user_notification.html
rm ./app/Mage.php
rm ./cron.php
rm ./downloader/lib/Mage/HTTP/Client/Curl.php
rm ./downloader/Maged/Connect.php
rm ./downloader/Maged/Controller.php
rm ./downloader/Maged/Controller.php.orig
rm ./downloader/Maged/Model/Session.php
rm ./downloader/Maged/Model/Session.php.orig
rm ./downloader/template/login.phtml
rm ./includes/config.php
rm ./js/lib/jquery/jquery-1.12.1.js
rm ./js/lib/jquery/jquery-1.12.1.min.js
rm ./js/lib/jquery/jquery-1.12.1.min.map
rm ./js/lib/uploader/flow.min.js
rm ./js/lib/uploader/fusty-flow-factory.js
rm ./js/lib/uploader/fusty-flow.js
rm ./js/mage/adminhtml/backup.js
rm ./js/mage/adminhtml/product.js
rm ./js/mage/adminhtml/uploader/instance.js
rm ./js/varien/payment.js
rm ./lib/Unserialize/Parser.php
rm ./lib/Unserialize/Reader/Arr.php
rm ./lib/Unserialize/Reader/ArrValue.php
rm ./lib/Unserialize/Reader/Null.php
rm ./lib/Varien/Filter/FormElementName.php
rm ./lib/Varien/Image/Adapter/Gd2.php
rm ./lib/Zend/Mail/Transport/Sendmail.php
rm ./skin/adminhtml/default/default/boxes.css
rm ./skin/adminhtml/default/default/xmlconnect/boxes.css
rm ./skin/adminhtml/default/enterprise/images/placeholder/thumbnail.jpg
rm ./skin/frontend/base/default/js/opcheckout.js
rm ./skin/frontend/base/default/js/opcheckout.js.orig skin/adminhtml/default/default/media/flex.swf
rm skin/adminhtml/default/default/media/uploader.swf
rm skin/adminhtml/default/default/media/uploaderSingle.swf
cp app/etc/ph_patch_backup_jan19v1_1924_07-01-2019_11-15.tar.gz .
tar -xvf ph_patch_backup_jan19v1_1924_07-01-2019_11-15.tar.gz
rm -rf var/cache/*
php -f shell/compiler.php -- clear
rm -r includes/src/*
ionice -c3 nice -n19 chown -R $correct_owner "/var/www/hifi-freaks.se/public_html"
ionice -c3 nice -n19 chmod -R 750 "/var/www/hifi-freaks.se/public_html"
rm ph_patch_backup_jan19v1_1924_07-01-2019_11-15.tar.gz
rm /var/www/hifi-freaks.se/public_html/app/etc/ph_patch_backup_jan19v1_1924_07-01-2019_11-15.tar.gz

#Clear LB static cache for js and css elements
for file in $jscsslist_bu; do
  curl --connect-timeout 1 -s -H 'pragma: no-cache' -I -L http://hifi-freaks.dev.magepartner.net/$file > /dev/null
done

echo "Dette site skal aldrig i evighed patches mere" | mail -s "hifi-freaks.se" pleasenopatch@powerhosting.dk
if [ "$revertmail" = "owner@example.com" ]; then
  echo "no valid email-address"
elif [ ! -z $revertmail ]; then
  curl -s http://resources.intl.powr.host/patches/ph/jan19v1/revertemail.html | sed -e "s~GENERALNAME~HiFi-freaks.se~g" -e "s|BASEURL|hifi-freaks.dev.magepartner.net|g" -e "s|PATCHDATE|07/01-19|g" -e "s|MAGEDIR|/var/www/hifi-freaks.se/public_html|g" -e "s/HOSTNAME/phhw-121202.intl.powerhosting.dk/g" -e "s/REVERTTIMESTAMP/${REVERTTIME}/g" | bsd-mailx -r 'patchbot@powerhosting.dk' -a 'Content-Type: text/html; charset=UTF-8' -a 'Content-Transfer-Encoding: binary' -a 'MIME-Version: 1.0' -s "Sikkerhedspatchen er netop rullet tilbage"     $revertmail
else "no valid email-address"
fi

cat << EOM >> app/etc/ph_patch.report
${REVERTTIME} /var/www/hifi-freaks.se/public_html jan19v1_1924 REVERTED
EOM
cd $initdir
rm -- "$0"
