# -*- mode: ruby -*-
# vi: set ft=ruby :

Vagrant.configure("2") do |config|
    config.vm.box = 'wheezy64'
    config.vm.box_url = 'http://puppet-vagrant-boxes.puppetlabs.com/debian-70rc1-x64-vbox4210.box'

    config.vm.provider :virtualbox do |vb|
        vb.name = 'DevicePresence'
        # Pass custom arguments to VBoxManage before booting VM
        vb.customize [
            'modifyvm', :id,
            '--memory', '1024'
        ]
    end

    config.vm.network :forwarded_port, guest: 9999, host: 9999
    config.vm.network :public_network

    config.vm.define :devicepresence do |project_config|
        project_config.vm.hostname = "devicepresence.local"

        project_config.vm.synced_folder ".", "/vagrant"

        project_config.vm.provision "shell", path: "dev/puppet/librarian-puppet.sh"
        project_config.vm.provision :puppet do |puppet|
            puppet.manifests_path = "dev/puppet/manifests"
            puppet.module_path = "dev/puppet/modules"
            puppet.manifest_file = "default.pp"
            # For debugging
            #puppet.options = "--verbose --debug"
        end
    end
end
