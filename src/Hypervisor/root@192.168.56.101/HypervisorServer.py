import HTTPListener
import re
import sys
import os
import BaseHTTPServer
import libvirtmod as virt
import json

###########################################
#       GENERAL CONFIG                    #
###########################################
QEMU_PATH = ""
IMAGE_DIR = "/home/madhavan/VM/"
VIRT_ENV = ""
HOST_NAME = ""
PORT = 9000
VM_TYPES_FILEPATH = ""
HYPERVISORS_LIST = {}
class HypervisorHandler:
    def __init__(self,vm_types="config/vm_types.php"):
        self.vm_types_file = vm_types
	self.type = 0
	self.vmid = 0 
	self.ram_usage = 0
	self.hd_usage = 0
	self.vm_count = 0 
	self.ram_capacity = 0 
	self.cpus = 0 
	self.hd_capacity = 0
	self.ram_left = 0 
	self.hd_left = 0 
	self.doms = {}
	self.name = ""
	self.get_local_config()

    def get_XML_def(self, parameters):
        """ This function will return the Hypervisor XML to be used in domain creation based on the parameters that it recieves"""
        global QEMU_PATH,VIRT_ENV
	 
	all_types_of_vm = self.get_all_vm_types()


	new_vm_type = parameters['vm_type'][0]
	new_vm_name = parameters['name'][0]
	new_vm_vmid = parameters['vmid'][0]
	self.name = new_vm_name
	self.type = new_vm_type
	self.vmid = new_vm_vmid
	
	vm_types = json.loads(all_types_of_vm)[u'types']
        for vm_type in vm_types:
	    print vm_type
	    if vm_type[u'tid'] == int(new_vm_type):
	        new_vm_ram = str(int(vm_type[u'ram'])*1000)
	        new_vm_disk =  vm_type[u'disk']
	        new_vm_cpu  =  vm_type[u'cpu']
	
	print "ram", self.ram_left
        self.ram_left -= int(new_vm_ram)
	print "ram", new_vm_ram
	print "ram", self.ram_left
	self.ram_usage = int(new_vm_ram)
	self.hd_usage = int(new_vm_disk)
	new_vm_image_path = IMAGE_DIR+parameters['image_name'][0]
	virtualization_platform = VIRT_ENV
	virtualization_platorm_URI = "qemu:///session"
        conn = virt.virConnectOpen(virtualization_platorm_URI)

	xml_def = virt.virConnectGetCapabilities(conn)
        environment = re.findall("<domain type='(.*)'",xml_def)[0]
        os_type = re.findall("<os_type>(.*)</os_type>",xml_def)[0]
        emulator = re.findall("<emulator>(.*)</emulator>",xml_def)[0]

        VIRT_ENV = environment
	QEMU_PATH = emulator


	######UPDATE HD HERE##############
	xml_def = """
	<domain type='%s'><name>%s</name><memory>%s</memory><vcpu>%s</vcpu><os><type arch='x86_64' machine='pc'>hvm</type>	<boot dev='hd'/></os><features>	<acpi/><apic/><pae/></features><on_poweroff>destroy</on_poweroff><on_reboot>restart</on_reboot><on_crash>restart</on_crash><devices><emulator>%s</emulator><disk type='file' device='disk'><driver name='%s' type='raw'/><source file='%s'/><target dev='hda' bus='ide'/><address type='drive' controller='0' bus='0' unit='0'/></disk></devices></domain>
        """%(environment,str(new_vm_name),str(new_vm_ram), str(new_vm_cpu),emulator,environment,str(new_vm_image_path))
	return xml_def

    def get_details(self):
        json_ret_val = """{
    "vmid":%s,
    "name":"%s",
    "vm_type":%s
}"""%(str(self.vmid),str(self.name),str(self.type))
        return json_ret_val

    def start_VM(self, xml_def,vmid):
        global VIRT_ENV
        #establish a connection to qemu/Kvm/Xen
	ret_val = "0"
	virtualization_platform = VIRT_ENV
	virtualization_platorm_URI = virtualization_platform+":///session"
        conn = virt.virConnectOpen(virtualization_platorm_URI)
	dom = virt.virDomainCreateXML(conn,xml_def,0)
	self.doms[vmid] = dom
	if dom:
	    ret_val="1"
	return ret_val

    def destroy_VM(self, vmid):
	virtualization_platform = VIRT_ENV
	virtualization_platorm_URI = virtualization_platform+":///session"
        conn = virt.virConnectOpen(virtualization_platorm_URI)
	dom = virt.virDomainLookupByName(conn,self.name)
	ret_val = '{\n"status":0\n}\n'
	if dom:
	    ret_val = '{\n"status":1\n}\n'
	    HYPERVISORS_LIST.pop(str(vmid['vmid'][0]))
	    virt.virDomainDestroy(dom)
	return ret_val
        


    def get_resources_left(self):
        ret_val = (self.cpus,self.ram_left,self.hd_left)
	return ret_val
    def get_resources_used(self):
        ret_val = (self.ram_usage,self.hd_usage)
	return ret_val


    def get_all_vm_types(self):
        fin = open(self.vm_types_file)
	contents = fin.read()
	return contents
    def get_local_config(self):
        """ To find the settings of the current hypervisor"""
        os.system("bin/store.sh")
	fin = open("bin/info.data")
	lines = fin.readlines()
	line1 = lines[0]
	cpu = line1[:-1]

	self.cpus = int(cpu)
	line2 = lines[1]
	fields = line2.split(" ")
	while '' in fields:
	    fields.remove('')
	ram = fields[1]

	line3 = lines[2]
	fields=line3.split(" ")
	while '' in fields:
	    fields.remove('')
	hd = fields[3]
	
	self.ram_capacity = int(ram)
	self.hd_capacity = hd
	self.ram_left = int(ram)
	self.hd_left = hd


	return "CPU:%s;RAM:%s;HD:%s"%(cpu,ram,hd)
        

    def processing_core(self, path, parameters):
        response = "An error occured!"
        if path == "/vm/types":
	    response = self.get_all_vm_types()
	elif path == "/pm/available":
	    response = self.get_resources_left()
	elif path == "/pm/all_available":
	    response = self.get_local_config()

	elif path == "/vm_internal/create":
            xml_definition = self.get_XML_def(parameters)
	    response = self.start_VM(xml_definition,parameters['vmid'][0])
	elif path == "/pm/name":
	    response = self.name
	elif path == "/vm_internal/query":
	    response = self.get_details()
	elif path == "/vm_internal/destroy":
	    response = self.destroy_VM(parameters)
	return response
	
    


def get_VMids():
    vmids = "{\n\"vmids\":"+str(HYPERVISORS_LIST.keys())+"\n}\n"
    return vmids
        
def get_query(pmid):
    """ To find the settings of the current hypervisor"""
    os.system("bin/store.sh")
    fin = open("bin/info.data")
    lines = fin.readlines()
    line1 = lines[0]
    cpu = int(line1[:-1])

    line2 = lines[1]
    fields = line2.split(" ")
    while '' in fields:
	fields.remove('')
    ram = int(fields[1])

    line3 = lines[2]
    fields=line3.split(" ")
    while '' in fields:
	fields.remove('')
    hd = int(fields[3][:-1])

    json_ret_val = "{\n"
    json_ret_val += '"pmid":'+str(pmid)+",\n"
    json_ret_val += '"capacity":{\n'+'"cpu":'+str(cpu)+',\n"ram":'+str(ram)+',\n"disk":'+str(hd)+"\n},\n"

    for hyper in HYPERVISORS_LIST.keys():
        hypervisor = HYPERVISORS_LIST[hyper]
	(hyp_ram,hyp_hd) = hypervisor.get_resources_used()
	ram -= hyp_ram
	hd -= hyp_hd
    
    json_ret_val += '"free":{\n'+'"cpu":'+str(cpu)+',\n"ram":'+str(ram)+',\n"disk":'+str(hd)+"\n},\n"	

    json_ret_val += '"vms":'+str(len(HYPERVISORS_LIST))
    json_ret_val += "\n}\n"

    return json_ret_val


def get_image_presence(parameters):
    image_name = parameters['image_name'][0]
    images_dir = IMAGE_DIR
    if image_name in os.listdir(images_dir):
        return "YES"
    else:
        return IMAGE_DIR 




    


class HypervisorHTTPServer(HTTPListener.MyHTTPHandler):
    def process_request(self, absolute_path, get_param):
	response = "An error Occured!"
	print absolute_path
	if absolute_path == "/pm_internal/listvms":
	    response =  get_VMids()
	elif absolute_path == "/image_internal/present":
	    response = get_image_presence(get_param)
	elif absolute_path == "/pm_internal/query":
	    if "pmid" not in get_param.keys():
	        return "Internal Error: PMID not found"
	    pmid = get_param['pmid'][0]
	    return get_query(pmid)

	    
	if "vmid" in get_param.keys():
	    vmid = get_param['vmid'][0]
	    if vmid in HYPERVISORS_LIST.keys():
		hypervisor = HYPERVISORS_LIST[vmid]
		response = hypervisor.processing_core(absolute_path, get_param)
	    else:
		hypervisor = HypervisorHandler(VM_TYPES_FILEPATH)
		HYPERVISORS_LIST[vmid] = hypervisor
		response = hypervisor.processing_core(absolute_path, get_param)
	return response

def initiator():
    global VIRT_ENV
    fin = open("bin/virtual.data")
    contents = fin.read()
    contents = content.lower()
    ret_val_xen = contents.find("xen")
    VIRT_ENV = "qemu"
    if ret_val_xen >=0:
        VIRT_ENV="xen"
    fin.close()
    fin = open("bin/configs")
    for line in fin.readlines():
        key = line.split("=")[0]
	value = line.split("=")[-1]
	if key=='virt_path' :
	    QEMU_PATH = value
	elif key == 'image_dir':
	    IMAGE_DIR = value




def main():
    server_class = BaseHTTPServer.HTTPServer
    global HOST_NAME, PORT, VM_TYPES_FILEPATH,HYPERVISORS_LIST
    VM_TYPES_FILEPATH = sys.argv[1]
    try:
        httpd = server_class((HOST_NAME,PORT), HypervisorHTTPServer)
        httpd.serve_forever()
    except Exception as e:
        print e


if __name__=="__main__":
    main()
