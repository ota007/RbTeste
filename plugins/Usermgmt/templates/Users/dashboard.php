
<div class="card">
	<div class="card-header text-white bg-dark">
		<span class="card-title">
			<?php echo __('Dashboard');?>
		</span>
	</div>

	<div class="card-body">
		<?php 
		if($this->UserAuth->isLogged()) {
			echo __('Hello').' '.$var['first_name'].' '.$var['last_name'];
			echo "<br/><br/>";

			$lastLoginTime = $this->UserAuth->getLastLoginTime();
			if($lastLoginTime) {
				echo __('Your last login time is ').$lastLoginTime;
				echo "<br/><br/>";
			}

			echo "<h4><span class='badge badge-primary'>".__('My Account')."</span></h4><br/>";

			if($this->UserAuth->HP('Users', 'myprofile', 'Usermgmt')) {
				echo $this->Html->link(__('My Profile'), ['controller'=>'Users', 'action'=>'myprofile', 'plugin'=>'Usermgmt'], ['class'=>'btn btn-secondary btn-sm mr-2 mb-2']);
			}

			if($this->UserAuth->HP('Users', 'editProfile', 'Usermgmt')) {
				echo $this->Html->link(__('Edit Profile'), ['controller'=>'Users', 'action'=>'editProfile', 'plugin'=>'Usermgmt'], ['class'=>'btn btn-secondary btn-sm mr-2 mb-2']);
			}

			if($this->UserAuth->HP('Users', 'changePassword', 'Usermgmt')) {
				echo $this->Html->link(__('Change Password'), ['controller'=>'Users', 'action'=>'changePassword', 'plugin'=>'Usermgmt'], ['class'=>'btn btn-secondary btn-sm mr-2 mb-2']);
			}

			if(ALLOW_DELETE_ACCOUNT && $this->UserAuth->HP('Users', 'deleteAccount', 'Usermgmt') && !$this->UserAuth->isAdmin()) {
				echo $this->Form->postLink(__('Delete Account'), ['controller'=>'Users', 'action'=>'deleteAccount', 'plugin'=>'Usermgmt'], ['escape'=>false, 'class'=>'btn btn-secondary btn-sm mr-2 mb-2', 'confirm'=>__('Are you sure you want to delete your account?')]);
			}

			echo "<hr/>";

			if($this->UserAuth->isAdmin()) {
				echo "<h4><span class='badge badge-primary'>".__('User Management')."</span></h4><br/>";

				if($this->UserAuth->HP('Users', 'addUser', 'Usermgmt')) {
					echo $this->Html->link(__('Add User'), ['controller'=>'Users', 'action'=>'addUser', 'plugin'=>'Usermgmt'], ['class'=>'btn btn-secondary btn-sm mr-2 mb-2']);
				}

				if($this->UserAuth->HP('Users', 'addMultipleUsers', 'Usermgmt')) {
					echo $this->Html->link(__('Add Multiple Users'), ['controller'=>'Users', 'action'=>'addMultipleUsers', 'plugin'=>'Usermgmt'], ['class'=>'btn btn-secondary btn-sm mr-2 mb-2']);
				}

				if($this->UserAuth->HP('Users', 'index', 'Usermgmt')) {
					echo $this->Html->link(__('All Users'), ['controller'=>'Users', 'action'=>'index', 'plugin'=>'Usermgmt'], ['class'=>'btn btn-secondary btn-sm mr-2 mb-2']);
				}

				if($this->UserAuth->HP('Users', 'online', 'Usermgmt')) {
					echo $this->Html->link(__('Online Users'), ['controller'=>'Users', 'action'=>'online', 'plugin'=>'Usermgmt'], ['class'=>'btn btn-secondary btn-sm mr-2 mb-2']);
				}

				if($this->UserAuth->HP('UserGroups', 'add', 'Usermgmt')) {
					echo $this->Html->link(__('Add Group'), ['controller'=>'UserGroups', 'action'=>'add', 'plugin'=>'Usermgmt'], ['class'=>'btn btn-secondary btn-sm mr-2 mb-2']);
				}

				if($this->UserAuth->HP('UserGroups', 'index', 'Usermgmt')) {
					echo $this->Html->link(__('All Groups'), ['controller'=>'UserGroups', 'action'=>'index', 'plugin'=>'Usermgmt'], ['class'=>'btn btn-secondary btn-sm mr-2 mb-2']);
				}

				echo "<hr/>";

				echo "<h4><span class='badge badge-primary'>".__('Group Permissions')."</span></h4><br/>";
				
				if($this->UserAuth->HP('UserGroupPermissions', 'groups', 'Usermgmt')) {
					echo $this->Html->link(__('Group Permissions'), ['controller'=>'UserGroupPermissions', 'action'=>'groups', 'plugin'=>'Usermgmt'], ['class'=>'btn btn-secondary btn-sm mr-2 mb-2']);
				}

				if($this->UserAuth->HP('UserGroupPermissions', 'subgroups', 'Usermgmt')) {
					echo $this->Html->link(__('Subgroup Permissions'), ['controller'=>'UserGroupPermissions', 'action'=>'subgroups', 'plugin'=>'Usermgmt'], ['class'=>'btn btn-secondary btn-sm mr-2 mb-2']);
				}

				echo "<hr/>";

				echo "<h4><span class='badge badge-primary'>".__('Email Communication')."</span></h4><br/>";
				
				if($this->UserAuth->HP('UserEmails', 'send', 'Usermgmt')) {
					echo $this->Html->link(__('Send Email'), ['controller'=>'UserEmails', 'action'=>'send', 'plugin'=>'Usermgmt'], ['class'=>'btn btn-secondary btn-sm mr-2 mb-2']);
				}

				if($this->UserAuth->HP('UserEmails', 'index', 'Usermgmt')) {
					echo $this->Html->link(__('View Sent Emails'), ['controller'=>'UserEmails', 'action'=>'index', 'plugin'=>'Usermgmt'], ['class'=>'btn btn-secondary btn-sm mr-2 mb-2']);
				}

				if($this->UserAuth->HP('ScheduledEmails', 'index', 'Usermgmt')) {
					echo $this->Html->link(__('Scheduled Emails'), ['controller'=>'ScheduledEmails', 'action'=>'index', 'plugin'=>'Usermgmt'], ['class'=>'btn btn-secondary btn-sm mr-2 mb-2']);
				}

				if($this->UserAuth->HP('UserContacts', 'index', 'Usermgmt')) {
					echo $this->Html->link(__('Contact Enquiries'), ['controller'=>'UserContacts', 'action'=>'index', 'plugin'=>'Usermgmt'], ['class'=>'btn btn-secondary btn-sm mr-2 mb-2']);
				}

				if($this->UserAuth->HP('UserEmailTemplates', 'index', 'Usermgmt')) {
					echo $this->Html->link(__('Email Templates'), ['controller'=>'UserEmailTemplates', 'action'=>'index', 'plugin'=>'Usermgmt'], ['class'=>'btn btn-secondary btn-sm mr-2 mb-2']);
				}

				if($this->UserAuth->HP('UserEmailSignatures', 'index', 'Usermgmt')) {
					echo $this->Html->link(__('Email Signatures'), ['controller'=>'UserEmailSignatures', 'action'=>'index', 'plugin'=>'Usermgmt'], ['class'=>'btn btn-secondary btn-sm mr-2 mb-2']);
				}

				echo "<hr/>";

				echo "<h4><span class='badge badge-primary'>".__('Static Pages Management')."</span></h4><br/>";
				
				if($this->UserAuth->HP('StaticPages', 'add', 'Usermgmt')) {
					echo $this->Html->link(__('Add Page'), ['controller'=>'StaticPages', 'action'=>'add', 'plugin'=>'Usermgmt'], ['class'=>'btn btn-secondary btn-sm mr-2 mb-2']);
				}
				
				if($this->UserAuth->HP('StaticPages', 'index', 'Usermgmt')) {
					echo $this->Html->link(__('All Pages'), ['controller'=>'StaticPages', 'action'=>'index', 'plugin'=>'Usermgmt'], ['class'=>'btn btn-secondary btn-sm mr-2 mb-2']);
				}
				
				echo "<hr/>";

				echo "<h4><span class='badge badge-primary'>".__('Admin Settings')."</span></h4><br/>";
				
				if($this->UserAuth->HP('UserSettings', 'index', 'Usermgmt')) {
					echo $this->Html->link(__('All Settings'), ['controller'=>'UserSettings', 'action'=>'index', 'plugin'=>'Usermgmt'], ['class'=>'btn btn-secondary btn-sm mr-2 mb-2']);
				}
				
				if($this->UserAuth->HP('UserSettings', 'cakelog', 'Usermgmt')) {
					echo $this->Html->link(__('Cake Logs'), ['controller'=>'UserSettings', 'action'=>'cakelog', 'plugin'=>'Usermgmt'], ['class'=>'btn btn-secondary btn-sm mr-2 mb-2']);
				}
				
				if($this->UserAuth->HP('Users', 'deleteCache', 'Usermgmt')) {
					echo $this->Html->link(__('Delete Cache'), ['controller'=>'Users', 'action'=>'deleteCache', 'plugin'=>'Usermgmt'], ['class'=>'btn btn-secondary btn-sm mr-2 mb-2']);
				}
			}
		}?>
	</div>
</div>