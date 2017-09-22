<?php 

class Hans2103_RssAuthAdmin_Helper_Data extends Mage_Rss_Helper_Data
{
    /**
     * Authenticate admin and check ACL
     *
     * @param string $path
     */
    public function authAdmin($path)
    {
        if (!$this->_rssSession->isAdminLoggedIn() || !$this->_adminSession->isLoggedIn()) {
            list($username, $password) = $this->authValidate();
            Mage::getSingleton('adminhtml/url')->setNoSecret(true);
            $user = $this->_adminSession->login($username, $password);
        } else {
            $user = $this->_rssSession->getAdmin();
        }
        if ($user && $user->getId() && $user->getIsActive() == '1' && $this->_adminSession->isAllowed($path)) {
        	$adminUserExtra = $user->getExtra();

            if ($adminUserExtra && !is_array($adminUserExtra)) {
                //$adminUserExtra = Mage::helper('core/unserializeArray')->unserialize($user->getExtra());
	            try {
		            $adminUserExtra = Mage::helper('core/unserializeArray')->unserialize($user->getExtra());
	            } catch (Exception $e) {
		            $adminUserExtra = [];
	            }
            }
            if (!isset($adminUserExtra['indirect_login'])) {
                $adminUserExtra = array_merge($adminUserExtra, array('indirect_login' => true));
                $user->saveExtra($adminUserExtra);
            }
            $this->_adminSession->setIndirectLogin(true);
            $this->_rssSession->setAdmin($user);
        } else {
            $this->authFailed();
        }
    }
}
