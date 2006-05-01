desc "Deploy basic application directories" 
task :deploy => :environment do
	print "Synchronizing\n"
	onserver = "acooper@hkcreations.org:/home/acooper/ffff.hkcreations.org/"
	local = "#{RAILS_ROOT}/"
	cmd = "rsync -arvz -e ssh #{local} #{onserver} --exclude '.DS_Store' --exclude '*.svn*' --exclude '/config/*' --exclude '/tmp/*' --exclude '/log/*' --exclude '/images/*' --delete" 

	rsync = IO.popen(cmd, "r")
	while line = rsync.gets
		print line
	end
	rsync.close     
end
