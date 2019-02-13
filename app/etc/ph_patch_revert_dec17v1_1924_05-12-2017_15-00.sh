#!/bin/bash
# This file reverts the bakup applied by Powerhosting 05-12-2017_15-00
initdir=$(pwd)
cd "/var/www/hifi-freaks.se/public_html"
REVERTTIME=$(date +%d-%m-%Y_%H-%M)
correct_owner=$(stat -c "%U:%G" index.php)
revertmail=support@hififreaks.dk
jscsslist_bu=$(tar -tf app/etc/ph_patch_backup_dec17v1_1924_05-12-2017_15-00.tar.gz | grep "\.js\|\.css" | cut -d/ -f2-)

rm ./app/code/core/Mage/Adminhtml/Block/Catalog/Product/Helper/Form/Gallery/Content.php
rm ./app/code/core/Mage/Adminhtml/Block/Checkout/Formkey.php
rm ./app/code/core/Mage/Adminhtml/Block/Cms/Wysiwyg/Images/Content/Uploader.php
rm ./app/code/core/Mage/Adminhtml/Block/Media/Uploader.php
rm ./app/code/core/Mage/Adminhtml/Block/Notification/Grid/Renderer/Notice.php
rm ./app/code/core/Mage/Adminhtml/Block/Notification/Symlink.php
rm ./app/code/core/Mage/Adminhtml/Block/Report/Review/Detail.php
rm ./app/code/core/Mage/Adminhtml/Block/Report/Tag/Product/Detail.php
rm ./app/code/core/Mage/Adminhtml/Block/Review/Add.php
rm ./app/code/core/Mage/Adminhtml/Block/Review/Edit/Form.php
rm ./app/code/core/Mage/Adminhtml/Block/Urlrewrite/Category/Tree.php
rm ./app/code/core/Mage/Adminhtml/Block/Widget/Form/Container.php
rm ./app/code/core/Mage/Adminhtml/Block/Widget/Grid/Column/Filter/Date.php
rm ./app/code/core/Mage/Adminhtml/Controller/Action.php
rm ./app/code/core/Mage/Adminhtml/controllers/Catalog/Product/GalleryController.php
rm ./app/code/core/Mage/Adminhtml/controllers/CustomerController.php
rm ./app/code/core/Mage/Adminhtml/controllers/DashboardController.php
rm ./app/code/core/Mage/Adminhtml/controllers/IndexController.php
rm ./app/code/core/Mage/Adminhtml/controllers/Media/UploaderController.php
rm ./app/code/core/Mage/Adminhtml/controllers/Newsletter/QueueController.php
rm ./app/code/core/Mage/Adminhtml/controllers/Newsletter/TemplateController.php
rm ./app/code/core/Mage/Adminhtml/Model/Config/Data.php
rm ./app/code/core/Mage/Adminhtml/Model/LayoutUpdate/Validator.php
rm ./app/code/core/Mage/Adminhtml/Model/System/Config/Backend/Filename.php
rm ./app/code/core/Mage/Adminhtml/Model/System/Config/Backend/Serialized.php
rm ./app/code/core/Mage/Adminhtml/Model/System/Config/Backend/Symlink.php
rm ./app/code/core/Mage/Admin/Model/Session.php
rm ./app/code/core/Mage/Api/Helper/Data.php
rm ./app/code/core/Mage/Api/Model/Server/Adapter/Soap.php
rm ./app/code/core/Mage/Api/Model/Wsdl/Config/Base.php
rm ./app/code/core/Mage/Api/Model/Wsdl/Config.php
rm ./app/code/core/Mage/Catalog/etc/config.xml
rm ./app/code/core/Mage/Catalog/etc/system.xml
rm ./app/code/core/Mage/Catalog/Helper/Image.php
rm ./app/code/core/Mage/Catalog/Model/Product/Api/V2.php
rm ./app/code/core/Mage/Centinel/Model/Api/Client.php
rm ./app/code/core/Mage/Centinel/Model/Api.php
rm ./app/code/core/Mage/Checkout/controllers/CartController.php
rm ./app/code/core/Mage/Checkout/controllers/MultishippingController.php
rm ./app/code/core/Mage/Checkout/controllers/OnepageController.php
rm ./app/code/core/Mage/Checkout/controllers/OnepageController.php.orig
rm ./app/code/core/Mage/Checkout/etc/system.xml
rm ./app/code/core/Mage/Cms/Model/Wysiwyg/Images/Storage.php
rm ./app/code/core/Mage/Cms/Model/Wysiwyg/Images/Storage.php.orig
rm ./app/code/core/Mage/Core/Block/Abstract.php
rm ./app/code/core/Mage/Core/Controller/Front/Action.php
rm ./app/code/core/Mage/Core/Controller/Request/Http.php
rm ./app/code/core/Mage/Core/etc/config.xml
rm ./app/code/core/Mage/Core/etc/system.xml
rm ./app/code/core/Mage/Core/functions.php
rm ./app/code/core/Mage/Core/Helper/String.php
rm ./app/code/core/Mage/Core/Helper/Url.php
rm ./app/code/core/Mage/Core/Model/Config.php
rm ./app/code/core/Mage/Core/Model/Email/Template/Abstract.php
rm ./app/code/core/Mage/Core/Model/Encryption.php
rm ./app/code/core/Mage/Core/Model/File/Validator/Image.php
rm ./app/code/core/Mage/Core/Model/Input/Filter/MaliciousCode.php
rm ./app/code/core/Mage/Core/Model/Session/Abstract/Varien.php
rm ./app/code/core/Mage/Core/sql/core_setup/upgrade-1.6.0.6.1.1-1.6.0.6.1.2.php
rm ./app/code/core/Mage/Customer/Block/Address/Book.php
rm ./app/code/core/Mage/Customer/controllers/AddressController.php
rm ./app/code/core/Mage/Customer/Model/Customer.php
rm ./app/code/core/Mage/Customer/Model/Session.php
rm ./app/code/core/Mage/Dataflow/Model/Convert/Adapter/Zend/Cache.php
rm ./app/code/core/Mage/Dataflow/Model/Convert/Container/Abstract.php
rm ./app/code/core/Mage/Dataflow/Model/Convert/Parser/Csv.php
rm ./app/code/core/Mage/Dataflow/Model/Convert/Parser/Xml/Excel.php
rm ./app/code/core/Mage/Dataflow/Model/Profile.php
rm ./app/code/core/Mage/Downloadable/Block/Adminhtml/Catalog/Product/Edit/Tab/Downloadable/Links.php
rm ./app/code/core/Mage/Downloadable/Block/Adminhtml/Catalog/Product/Edit/Tab/Downloadable/Samples.php
rm ./app/code/core/Mage/Downloadable/Helper/File.php
rm ./app/code/core/Mage/Eav/Model/Entity/Attribute/Backend/Serialized.php
rm ./app/code/core/Mage/ImportExport/Model/Import/Uploader.php
rm ./app/code/core/Mage/Log/Helper/Data.php
rm ./app/code/core/Mage/Oauth/Model/Server.php
rm ./app/code/core/Mage/Paygate/Model/Authorizenet.php
rm ./app/code/core/Mage/Payment/Block/Info/Checkmo.php
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
rm ./app/code/core/Mage/Usa/Model/Shipping/Carrier/Dhl/International.php
rm ./app/code/core/Mage/Usa/Model/Shipping/Carrier/Dhl.php
rm ./app/code/core/Mage/Usa/Model/Shipping/Carrier/Fedex.php
rm ./app/code/core/Mage/Usa/Model/Shipping/Carrier/Ups.php
rm ./app/code/core/Mage/Widget/Model/Widget/Instance.php
rm ./app/code/core/Mage/Wishlist/controllers/IndexController.php
rm ./app/code/core/Mage/Wishlist/Helper/Data.php
rm ./app/code/core/Mage/XmlConnect/Block/Adminhtml/Mobile/Edit/Tab/Design/Images.php
rm ./app/code/core/Mage/XmlConnect/Block/Adminhtml/Mobile/Edit/Tab/Design.php
rm ./app/code/core/Mage/XmlConnect/controllers/Adminhtml/MobileController.php
rm ./app/code/core/Mage/XmlConnect/Helper/Image.php
rm ./app/code/core/Zend/Form/Decorator/Form.php
rm ./app/code/core/Zend/Serializer/Adapter/PhpCode.php
rm ./app/design/adminhtml/default/default/layout/cms.xml
rm ./app/design/adminhtml/default/default/layout/main.xml
rm ./app/design/adminhtml/default/default/layout/xmlconnect.xml
rm ./app/design/adminhtml/default/default/template/backup/dialogs.phtml
rm ./app/design/adminhtml/default/default/template/catalog/product/edit/options/type/file.phtml
rm ./app/design/adminhtml/default/default/template/catalog/product/helper/gallery.phtml
rm ./app/design/adminhtml/default/default/template/cms/browser/content/uploader.phtml
rm ./app/design/adminhtml/default/default/template/customer/tab/view.phtml
rm ./app/design/adminhtml/default/default/template/downloadable/product/edit/downloadable/links.phtml
rm ./app/design/adminhtml/default/default/template/downloadable/product/edit/downloadable.phtml
rm ./app/design/adminhtml/default/default/template/downloadable/product/edit/downloadable/samples.phtml
rm ./app/design/adminhtml/default/default/template/login.phtml
rm ./app/design/adminhtml/default/default/template/media/uploader.phtml
rm ./app/design/adminhtml/default/default/template/notification/formkey.phtml
rm ./app/design/adminhtml/default/default/template/notification/symlink.phtml
rm ./app/design/adminhtml/default/default/template/notification/toolbar.phtml
rm ./app/design/adminhtml/default/default/template/oauth/authorize/form/login.phtml
rm ./app/design/adminhtml/default/default/template/oauth/authorize/form/login-simple.phtml
rm ./app/design/adminhtml/default/default/template/oauth/authorize/head-simple.phtml
rm ./app/design/adminhtml/default/default/template/page/head.phtml
rm ./app/design/adminhtml/default/default/template/resetforgottenpassword.phtml
rm ./app/design/adminhtml/default/default/template/sales/billing/agreement/view/tab/info.phtml
rm ./app/design/adminhtml/default/default/template/sales/order/view/history.phtml
rm ./app/design/adminhtml/default/default/template/sales/order/view/info.phtml
rm ./app/design/adminhtml/default/default/template/xmlconnect/edit/tab/content.phtml
rm ./app/design/adminhtml/default/default/template/xmlconnect/edit/tab/design/image_edit.phtml
rm ./app/design/adminhtml/default/default/template/xmlconnect/edit/tab/design.phtml
rm ./app/design/frontend/base/default/template/checkout/cart/shipping.phtml
rm ./app/design/frontend/base/default/template/checkout/multishipping/addresses.phtml
rm ./app/design/frontend/base/default/template/checkout/multishipping/billing.phtml
rm ./app/design/frontend/base/default/template/checkout/multishipping/shipping.phtml
rm ./app/design/frontend/base/default/template/checkout/onepage/billing.phtml
rm ./app/design/frontend/base/default/template/checkout/onepage/payment.phtml
rm ./app/design/frontend/base/default/template/checkout/onepage/payment.phtml.orig
rm ./app/design/frontend/base/default/template/checkout/onepage/shipping_method.phtml
rm ./app/design/frontend/base/default/template/checkout/onepage/shipping.phtml
rm ./app/design/frontend/base/default/template/persistent/checkout/onepage/billing.phtml
rm ./app/design/frontend/rwd/default/layout/page.xml
rm ./app/design/frontend/rwd/default/template/checkout/cart/shipping.phtml
rm ./app/design/frontend/rwd/default/template/checkout/multishipping/addresses.phtml
rm ./app/design/frontend/rwd/default/template/checkout/multishipping/billing.phtml
rm ./app/design/frontend/rwd/default/template/checkout/onepage/payment.phtml
rm ./app/design/frontend/rwd/default/template/checkout/onepage/payment.phtml.orig
rm ./app/design/frontend/rwd/default/template/checkout/onepage/shipping.phtml
rm ./app/design/frontend/rwd/default/template/persistent/checkout/onepage/billing.phtml
rm ./app/design/install/default/default/template/install/create_admin.phtml
rm ./app/etc/applied.patches.list
rm ./app/etc/config.xml
rm ./app/etc/modules/Mage_All.xml
rm ./app/locale/en_US/Mage_Adminhtml.csv
rm ./app/locale/en_US/Mage_Adminhtml.csv.orig
rm ./app/locale/en_US/Mage_Core.csv
rm ./app/locale/en_US/Mage_Core.csv.orig
rm ./app/locale/en_US/Mage_Customer.csv
rm ./app/locale/en_US/Mage_Dataflow.csv
rm ./app/locale/en_US/Mage_Media.csv
rm ./app/locale/en_US/Mage_Uploader.csv
rm ./app/locale/en_US/Mage_XmlConnect.csv
rm ./app/Mage.php
rm ./downloader/lib/Mage/HTTP/Client/Curl.php
rm ./downloader/Maged/Connect.php
rm ./downloader/Maged/Controller.php
rm ./downloader/Maged/Controller.php.orig
rm ./downloader/Maged/Model/Session.php
rm ./downloader/Maged/Model/Session.php.orig
rm ./downloader/template/login.phtml
rm ./includes/config.php
rm ./js/lib/jquery/jquery-1.12.0.js
rm ./js/lib/jquery/jquery-1.12.0.min.js
rm ./js/lib/jquery/jquery-1.12.0.min.map
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
rm ./lib/Zend/Mail/Transport/Sendmail.php
rm ./skin/adminhtml/default/default/boxes.css
rm ./skin/adminhtml/default/default/xmlconnect/boxes.css
rm ./skin/frontend/base/default/js/opcheckout.js
rm ./skin/frontend/base/default/js/opcheckout.js.orig skin/adminhtml/default/default/media/flex.swf
rm skin/adminhtml/default/default/media/uploader.swf
rm skin/adminhtml/default/default/media/uploaderSingle.swf
cp app/etc/ph_patch_backup_dec17v1_1924_05-12-2017_15-00.tar.gz .
tar -xvf ph_patch_backup_dec17v1_1924_05-12-2017_15-00.tar.gz
rm -r var/cache/*
php -f shell/compiler.php -- clear
rm -r includes/src/*
ionice -c3 nice -n19 chown -R $correct_owner "/var/www/hifi-freaks.se/public_html"
ionice -c3 nice -n19 chmod -R 750 "/var/www/hifi-freaks.se/public_html"
rm ph_patch_backup_dec17v1_1924_05-12-2017_15-00.tar.gz
rm /var/www/hifi-freaks.se/public_html/app/etc/ph_patch_backup_dec17v1_1924_05-12-2017_15-00.tar.gz

#Clear LB static cache for js and css elements
for file in $jscsslist_bu; do
  curl --connect-timeout 1 -s -H 'pragma: no-cache' -I -L https://hifi-freaks.se/$file > /dev/null
done

echo "Dette site skal aldrig i evighed patches mere" | mail -s "hifi-freaks.se" pleasenopatch@powerhosting.dk
if [ "$revertmail" = "owner@example.com" ]; then
  echo "no valid email-address"
elif [ ! -z $revertmail ]; then
  curl -s http://resources.intl.powr.host/patches/ph/dec17v1/revertemail.txt | sed -e "s~GENERALNAME~HiFi-freaks.se~g" -e "s|BASEURL|hifi-freaks.se|g" -e "s|PATCHDATE|05/12-17|g" -e "s|MAGEDIR|/var/www/hifi-freaks.se/public_html|g" -e "s/HOSTNAME/phhw-171004.intl.powerhosting.dk/g" -e "s/REVERTTIMESTAMP/${REVERTTIME}/g" | mail     -r"patchbot@powerhosting.dk (Powerhosting PatchBot)"     -S ttycharset=utf-8 -S sendcharsets=utf-8 -S encoding=8bit     -s "Sikkerhedspatchen på hifi-freaks.se er netop rullet tilbage"     $revertmail
else "no valid email-address"
fi

cat << EOM >> app/etc/ph_patch.report
${REVERTTIME} /var/www/hifi-freaks.se/public_html dec17v1_1924 REVERTED
EOM
cd $initdir
rm -- "$0"
