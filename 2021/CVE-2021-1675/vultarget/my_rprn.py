from impacket.dcerpc.v5.dtypes import DWORD, LPWSTR, ULONG
from impacket.dcerpc.v5.ndr import NDRCALL, NDRPOINTER, NDRSTRUCT, NDRUNION, NULL
from impacket.dcerpc.v5.rprn import DCERPCSessionError, PBYTE_ARRAY, STRING_HANDLE, checkNullString
from impacket.structure import Structure

APD_COPY_ALL_FILES = 0x00000004
APD_COPY_NEW_FILES = 0x00000008
APD_COPY_FROM_DIRECTORY = 0x00000010
APD_INSTALL_WARNED_DRIVER = 0x00008000


# 2.2.1.5.1 DRIVER_INFO_1
class DRIVER_INFO_1(NDRSTRUCT):
    structure = (
        ('pName', STRING_HANDLE),
    )


class PDRIVER_INFO_1(NDRPOINTER):
    referent = (
        ('Data', DRIVER_INFO_1),
    )


# 2.2.1.5.2 DRIVER_INFO_2
class DRIVER_INFO_2(NDRSTRUCT):
    structure = (
        ('cVersion', DWORD),
        ('pName', LPWSTR),
        ('pEnvironment', LPWSTR),
        ('pDriverPath', LPWSTR),
        ('pDataFile', LPWSTR),
        ('pConfigFile', LPWSTR),
    )


class PDRIVER_INFO_2(NDRPOINTER):
    referent = (
        ('Data', DRIVER_INFO_2),
    )


class DRIVER_INFO_UNION(NDRUNION):
    commonHdr = (
        ('tag', ULONG),
    )
    union = {
        1: ('pNotUsed', PDRIVER_INFO_1),
        2: ('Level2', PDRIVER_INFO_2),
    }


# https://docs.microsoft.com/en-us/openspecs/windows_protocols/ms-rprn/3a3f9cf7-8ec4-4921-b1f6-86cf8d139bc2
class DRIVER_CONTAINER(NDRSTRUCT):
    structure = (
        ('Level', DWORD),
        ('DriverInfo', DRIVER_INFO_UNION),
    )


class PDRIVER_CONTAINER(NDRPOINTER):
    referent = (
        ("Level", DWORD),
        ("DriverInfo", DRIVER_INFO_UNION)
    )


class RpcEnumPrinterDrivers(NDRCALL):
    opnum = 10
    structure = (
        ("pName", STRING_HANDLE),
        ("pEnvironment", LPWSTR),
        ("Level", DWORD),
        ("pDrivers", PBYTE_ARRAY),
        ("cbBuf", DWORD),
    )


class RpcEnumPrinterDriversResponse(NDRCALL):
    structure = (
        ('pDrivers', PBYTE_ARRAY),
        ('pcbNeeded', DWORD),
        ('pcReturned', DWORD),
        ('ErrorCode', ULONG),
    )


class RpcAddPrinterDriverEx(NDRCALL):
    opnum = 89
    structure = (
        ("pName", STRING_HANDLE),
        ("pDriverContainer", DRIVER_CONTAINER),
        ("dwFileCopyFlags", DWORD),
    )


class RpcAddPrinterDriverExResponse(NDRCALL):
    structure = (
        ('ErrorCode', ULONG),
    )


# https://docs.microsoft.com/en-us/openspecs/windows_protocols/ms-rprn/2825d22e-c5a5-47cd-a216-3e903fd6e030
class DRIVER_INFO_2_BLOB(Structure):
    structure = (
        ('cVersion', '<L'),
        ('NameOffset', '<L'),
        ('EnvironmentOffset', '<L'),
        ('DriverPathOffset', '<L'),
        ('DataFileOffset', '<L'),
        ('ConfigFileOffset', '<L'),
    )


    def __init__(self, data=None):
        Structure.__init__(self, data=data)


    def fromString(self, data, offset=0):
        Structure.fromString(self, data)
        self['ConfigFileArray'] = self.rawData[
                                  self['ConfigFileOffset'] + offset:self['DataFileOffset'] + offset].decode('utf-16-le')
        self['DataFileArray'] = self.rawData[self['DataFileOffset'] + offset:self['DriverPathOffset'] + offset].decode(
            'utf-16-le')
        self['DriverPathArray'] = self.rawData[
                                  self['DriverPathOffset'] + offset:self['EnvironmentOffset'] + offset].decode(
            'utf-16-le')
        self['EnvironmentArray'] = self.rawData[self['EnvironmentOffset'] + offset:self['NameOffset'] + offset].decode(
            'utf-16-le')
        # self['NameArray'] = self.rawData[self['NameOffset']+offset:len(self.rawData)].decode('utf-16-le')


class DRIVER_INFO_2_ARRAY(Structure):
    def __init__(self, data=None, pcReturned=None):
        Structure.__init__(self, data=data)
        self['drivers'] = list()
        remaining = data
        if data is not None:
            for i in range(pcReturned):
                attr = DRIVER_INFO_2_BLOB(remaining)
                self['drivers'].append(attr)
                remaining = remaining[len(attr):]


def hRpcAddPrinterDriverEx(dce, pName, pDriverContainer, dwFileCopyFlags):
    """
    RpcAddPrinterDriverEx installs a printer driver on the print server
    Full Documentation: https://docs.microsoft.com/en-us/openspecs/windows_protocols/ms-rprn/b96cc497-59e5-4510-ab04-5484993b259b

    :param DCERPC_v5 dce: a connected DCE instance.
    :param pName
    :param pDriverContainer
    :param dwFileCopyFlags

    :return: raises DCERPCSessionError on error.
    """
    request = RpcAddPrinterDriverEx()
    request['pName'] = checkNullString(pName)
    request['pDriverContainer'] = pDriverContainer
    request['dwFileCopyFlags'] = dwFileCopyFlags

    # return request
    return dce.request(request)


def hRpcEnumPrinterDrivers(dce, pName, pEnvironment, Level):
    """
    RpcEnumPrinterDrivers enumerates the printer drivers installed on a specified print server.
    Full Documentation: https://docs.microsoft.com/en-us/openspecs/windows_protocols/ms-rprn/857d00ac-3682-4a0d-86ca-3d3c372e5e4a

    :param DCERPC_v5 dce: a connected DCE instance.
    :param pName
    :param pEnvironment
    :param Level
    :return: raises DCERPCSessionError on error.
    """
    # get value for cbBuf
    request = RpcEnumPrinterDrivers()
    request['pName'] = checkNullString(pName)
    request['pEnvironment'] = pEnvironment
    request['Level'] = Level
    request['pDrivers'] = NULL
    request['cbBuf'] = 0
    try:
        dce.request(request)
    except DCERPCSessionError as e:
        if str(e).find('ERROR_INSUFFICIENT_BUFFER') < 0:
            raise
        bytesNeeded = e.get_packet()['pcbNeeded']
    else:
        raise

    # now do RpcEnumPrinterDrivers again
    request = RpcEnumPrinterDrivers()
    request['pName'] = checkNullString(pName)
    request['pEnvironment'] = pEnvironment
    request['Level'] = Level
    request['pDrivers'] = b'a' * bytesNeeded
    request['cbBuf'] = bytesNeeded

    # return request
    return dce.request(request)
