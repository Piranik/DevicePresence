# -*- mode: ruby -*-
# vi: set ft=ruby :

Vagrant.configure("2") do |baseconfig|
    baseconfig.vm.define :"devicepresence.local" do |config|
        # Box
        config.vm.box = "ubuntu/trusty64"
        config.vm.hostname = "devicepresence.local"

        # Network
        config.vm.network "forwarded_port", guest: 9999, host: 9991
        config.vm.network "public_network"
        config.vm.network "private_network", ip: "192.168.56.251"
        config.ssh.forward_agent = true

        # Virtualbox
        config.vm.provider :virtualbox do |vb|
            vb.name = config.vm.hostname
            vb.memory = 2048
            vb.customize [
                'modifyvm', :id,
                '--natdnshostresolver1', 'on',
                '--natdnsproxy1', 'on'
            ]
        end

        # Folders
        config.vm.synced_folder ".", "/vagrant", type: 'nfs', mount_options: ['nolock', 'actimeo=1']

        # Provision
        config.vm.provision "shell", path: "dev/puppet/librarian-puppet.sh"
        config.vm.provision :puppet do |puppet|
            puppet.manifests_path = "dev/puppet/manifests"
            puppet.module_path = "dev/puppet/modules"
            puppet.manifest_file = "default.pp"
            # For debugging
            #puppet.options = "--verbose --debug"
        end
    end
end
