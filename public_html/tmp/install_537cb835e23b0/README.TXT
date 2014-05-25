HOWTO INSTALL 
-------------

IMPORTANT NOTE: If you have an old PaidSystem already installed. After the installation
of PaidSystem/Invoicing/VirtualMoney, you need to go in paidsystem admin and click on export 
Paidsystem to Invoicing and VirtualMOney

Full documentation could be found on joomprod.com

Note: Before any update it's recommended to perform a backup !

If you have a previous version of adsmanager < 2.5 
******************************************************************

Please first upgrade (uninstall/reinstall) the adsmanager component to 2.5 (stable)
then follow the normal procedure

If it's the first install or if you have a version >= 2.6 
*********************************************************
1a- Install adsmanager component (adsmanager.zip), 
using the Joomla Extensions Installation Page

(for bronze and silver)
1b- Install paidsystem component (paidsystem.zip), 
using the Joomla Extensions Installation Page

1c- Install invoicing component (invoicing.zip), 
using the Joomla Extensions Installation Page

1d- Install virtualmoney component (virtualmoney.zip), 
using the Joomla Extensions Installation Page
This step is optional, only needed if you want a "credits system".

Community Builder Integration:
---------------------------------

2a- If you are using community builder, you have to install all communitybuilder_XXXX_plugin.zip for the third-party directory
using the Community Builder Installation Page

For Joomla 15
************
- Xmap plugin should be installed via xmap plugin page for Joomla 1.5,

Basic Error and FAQ:
********************

1) If you get the following error:
Warning: require_once(....../communitybuilder_adsmanager_plugin/plug.php) [function.require-once]
You have install the communitybuilder plugin via the adsmanager page instead of communitybuilder page.
You should delete manually the directory .../images/com_adsmanager/plugins/communitybuilder_adsmanager_plugin

2) You want to change the format of date
Edit your language file [JOOMLA_HOME]/language/[CODE_LANGUAGE]/[CODE_LANGUAGE].com_adsmanager.ini
ADSMANAGER_DATE_FORMAT_LC="%d-%m-%Y"

3) If you are using JoomFISH, you will notice an issue "no translate of subcategory in some case"
for that, there is a bug in joomfish

In joomfish.class.php , line 98

Replace :

foreach ($tableArray["fieldTablePairs"] as $key=>$value){
                $reftable = $tableArray["fieldTablePairs"][$key];
                $alias = $tableArray["tableAliases"][$reftable];

by :

foreach ($tableArray["reverseTableAliases"] as $alias=>$reftable){
    //$reftable = $tableArray["fieldTablePairs"][$key];
    //$alias = $tableArray["tableAliases"][$reftable];


Basic Installation Error:
*************************

1) If you get the following error:
Fatal error: Call to a member function display_form() on a non-object in /components/com_paidsystem/controller.php on line 100

You need to save at least one time the configuration in paidsystem administration

