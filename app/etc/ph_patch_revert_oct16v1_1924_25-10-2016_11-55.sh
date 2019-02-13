#!/bin/bash
# This file reverts the bakup applied by Powerhosting 25-10-2016_11-55
cd "/var/www/hifi-freaks.se/public_html"
REVERTTIME=$(date +%d-%m-%Y_%H-%M)
correct_owner=hifi-freaks:virtual
revertmail=owner@example.com
jscsslist_bu=$(tar -tf app/etc/ph_patch_backup_oct16v1_1924_25-10-2016_11-55.tar.gz | grep "\.js\|\.css" | cut -d/ -f2-)

rm ./downloader/lib/Mage/HTTP/Client/Curl.php
rm ./app/code/core/Mage/Adminhtml/Model/System/Config/Backend/Serialized.php
rm ./app/code/core/Mage/Adminhtml/controllers/IndexController.php
rm ./app/code/core/Mage/Adminhtml/controllers/DashboardController.php
rm ./app/code/core/Mage/Adminhtml/controllers/Media/UploaderController.php
rm ./app/code/core/Mage/Adminhtml/Block/Urlrewrite/Category/Tree.php
rm ./app/code/core/Mage/Adminhtml/Block/Cms/Wysiwyg/Images/Content/Uploader.php
rm ./app/code/core/Mage/Adminhtml/Block/Catalog/Product/Helper/Form/Gallery/Content.php
rm ./app/code/core/Mage/Adminhtml/Block/Media/Uploader.php
rm ./app/code/core/Mage/Downloadable/Block/Adminhtml/Catalog/Product/Edit/Tab/Downloadable/Links.php
rm ./app/code/core/Mage/Downloadable/Block/Adminhtml/Catalog/Product/Edit/Tab/Downloadable/Samples.php
rm ./app/code/core/Mage/Downloadable/Helper/File.php
rm ./app/code/core/Mage/Customer/controllers/AddressController.php
rm ./app/code/core/Mage/Customer/Block/Address/Book.php
rm ./app/code/core/Mage/Core/functions.php
rm ./app/code/core/Mage/Core/Model/Config.php
rm ./app/code/core/Mage/Core/Model/Encryption.php
rm ./app/code/core/Mage/Core/Model/Input/Filter/MaliciousCode.php
rm ./app/code/core/Mage/Core/Block/Abstract.php
rm ./app/code/core/Mage/Core/Helper/Url.php
rm ./app/code/core/Mage/Usa/Model/Shipping/Carrier/Dhl/International.php
rm ./app/code/core/Mage/Usa/Model/Shipping/Carrier/Dhl.php
rm ./app/code/core/Mage/Usa/Model/Shipping/Carrier/Ups.php
rm ./app/code/core/Mage/Usa/Model/Shipping/Carrier/Fedex.php
rm ./app/code/core/Mage/Usa/etc/config.xml
rm ./app/code/core/Mage/Usa/etc/system.xml
rm ./app/code/core/Mage/Oauth/Model/Server.php
rm ./app/code/core/Mage/XmlConnect/controllers/Adminhtml/MobileController.php
rm ./app/code/core/Mage/XmlConnect/Block/Adminhtml/Mobile/Edit/Tab/Design/Images.php
rm ./app/code/core/Mage/XmlConnect/Block/Adminhtml/Mobile/Edit/Tab/Design.php
rm ./app/code/core/Mage/Api/Model/Server/Adapter/Soap.php
rm ./app/code/core/Mage/Payment/Block/Info/Checkmo.php
rm ./app/code/core/Mage/Dataflow/Model/Profile.php
rm ./app/code/core/Mage/Catalog/Model/Product/Api/V2.php
rm ./app/code/core/Mage/Catalog/etc/config.xml
rm ./app/code/core/Mage/Catalog/etc/system.xml
rm ./app/code/core/Mage/Catalog/Helper/Image.php
rm ./app/code/core/Mage/Uploader/Model/Config/Misc.php
rm ./app/code/core/Mage/Uploader/Model/Config/Uploader.php
rm ./app/code/core/Mage/Uploader/Model/Config/Abstract.php
rm ./app/code/core/Mage/Uploader/Model/Config/Browsebutton.php
rm ./app/code/core/Mage/Uploader/etc/config.xml
rm ./app/code/core/Mage/Uploader/etc/jstranslator.xml
rm ./app/code/core/Mage/Uploader/Block/Multiple.php
rm ./app/code/core/Mage/Uploader/Block/Single.php
rm ./app/code/core/Mage/Uploader/Block/Abstract.php
rm ./app/code/core/Mage/Uploader/Helper/Data.php
rm ./app/code/core/Mage/Uploader/Helper/File.php
rm ./app/code/core/Mage/Paypal/Model/Express/Checkout.php
rm ./app/code/core/Mage/Paypal/Model/Resource/Payment/Transaction.php
rm ./app/code/core/Mage/Sales/Model/Resource/Recurring/Profile.php
rm ./app/code/core/Mage/Sales/Model/Resource/Quote/Payment.php
rm ./app/code/core/Mage/Sales/Model/Resource/Order/Payment.php
rm ./app/code/core/Mage/Sales/Model/Resource/Order/Payment/Transaction.php
rm ./app/code/core/Mage/Centinel/Model/Api.php
rm ./app/code/core/Mage/Centinel/Model/Api/Client.php
rm ./app/code/core/Mage/Wishlist/controllers/IndexController.php
rm ./app/code/core/Mage/Wishlist/Helper/Data.php
rm ./app/code/core/Mage/Paygate/Model/Authorizenet.php
rm ./app/design/adminhtml/default/default/layout/main.xml
rm ./app/design/adminhtml/default/default/layout/cms.xml
rm ./app/design/adminhtml/default/default/layout/xmlconnect.xml
rm ./app/design/adminhtml/default/default/template/xmlconnect/edit/tab/design.phtml
rm ./app/design/adminhtml/default/default/template/downloadable/product/edit/downloadable/links.phtml
rm ./app/design/adminhtml/default/default/template/downloadable/product/edit/downloadable/samples.phtml
rm ./app/design/adminhtml/default/default/template/downloadable/product/edit/downloadable.phtml
rm ./app/design/adminhtml/default/default/template/catalog/product/helper/gallery.phtml
rm ./app/design/adminhtml/default/default/template/media/uploader.phtml
rm ./app/design/adminhtml/default/default/template/cms/browser/content/uploader.phtml
rm ./app/locale/en_US/Mage_Media.csv
rm ./app/locale/en_US/Mage_Uploader.csv
rm ./app/etc/applied.patches.list
rm ./app/etc/modules/Mage_All.xml
rm ./lib/Unserialize/Parser.php
rm ./lib/Unserialize/Reader/ArrValue.php
rm ./lib/Unserialize/Reader/Null.php
rm ./lib/Unserialize/Reader/Arr.php
rm ./js/lib/uploader/fusty-flow.js
rm ./js/lib/uploader/fusty-flow-factory.js
rm ./js/lib/uploader/flow.min.js
rm ./js/mage/adminhtml/uploader/instance.js
rm ./js/mage/adminhtml/product.js
rm ./skin/adminhtml/default/default/xmlconnect/boxes.css
rm ./skin/adminhtml/default/default/boxes.css skin/adminhtml/default/default/media/flex.swf
rm skin/adminhtml/default/default/media/uploader.swf
rm skin/adminhtml/default/default/media/uploaderSingle.swf
cp app/etc/ph_patch_backup_oct16v1_1924_25-10-2016_11-55.tar.gz .
tar -xvf ph_patch_backup_oct16v1_1924_25-10-2016_11-55.tar.gz
rm -r var/cache/*
php -f shell/compiler.php -- clear
rm -r includes/src/*
ionice -c3 nice -n19 chown -R $correct_owner "/var/www/hifi-freaks.se/public_html"
ionice -c3 nice -n19 chmod -R 750 "/var/www/hifi-freaks.se/public_html"
rm ph_patch_backup_oct16v1_1924_25-10-2016_11-55.tar.gz
rm app/etc/ph_patch_backup_oct16v1_1924_25-10-2016_11-55.tar.gz

#Clear LB static cache for js and css elements
for file in $jscsslist_bu; do
  curl -s -H 'pragma: no-cache' -I -L http://hifi-freaks.prod29.magentohotel.dk/$file > /dev/null
done

echo "Dette site skal aldrig i evighed patches mere" | mail -s "hifi-freaks.se" pleasenopatch@powerhosting.dk
if [ "$revertmail" = "owner@example.com" ]; then
  echo "no valid email-address"
elif [ ! -z $revertmail ]; then
  curl -s http://resources.intl.powr.host/patches/ph/oct16v1/revertemail.txt | sed -e "s~GENERALNAME~Owner~g" -e "s|BASEURL|hifi-freaks.prod29.magentohotel.dk|g" -e "s|PATCHDATE|25/10-16|g" -e "s|MAGEDIR|/var/www/hifi-freaks.se/public_html|g" -e "s/REVERTTIMESTAMP/${REVERTTIME}/g" | mail     -r"patchbot@powerhosting.dk (Powerhosting PatchBot)"     -S ttycharset=utf-8 -S sendcharsets=utf-8 -S encoding=8bit     -s "Sikkerhedspatchen på hifi-freaks.prod29.magentohotel.dk er netop rullet tilbage"     $revertmail
else "no valid email-address"
fi

cat << EOM >> app/etc/ph_patch.report
${REVERTTIME} /var/www/hifi-freaks.se/public_html oct16v1_1924 REVERTED
EOM
rm -- "$0"
