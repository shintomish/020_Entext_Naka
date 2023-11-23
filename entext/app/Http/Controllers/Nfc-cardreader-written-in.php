<?php
/**
 * Reference:
 *   - http://www.libnfc.org/api/examples_page.html
 *   - https://github.com/nfc-tools/libnfc/blob/master/examples/nfc-poll.c
 *
 * Tested:
 *   - PaSoRi RC-S330
 *
 * Example Output:
 * NFC reader: Sony /  opened
 *  string(164) "FeliCa (212 kbps) target:
 *  ID (NFCID2): XX  XX  XX  XX  XX  XX  XX  XX
 *  Parameter (PAD): YY  YY  YY  YY  YY  YY  YY  YY
 *  System Code (SC): ZZ  ZZ
 *  "
 *
 */

/**
 * You can use this one when installing libnfc with brew.
 * `brew install libnfc` on your machine.
 */
$libraryPath = '/usr/local/Cellar/libnfc/1.8.0/lib/libnfc.dylib';

/**
 * Merged
 *  - https://github.com/nfc-tools/libnfc/blob/master/libnfc/nfc-internal.h
 *  - https://github.com/nfc-tools/libnfc/blob/2b5ad9ce0be19fbca5abc04b4ee0b59fb612e590/include/nfc/nfc-types.h
 *
 * @license https://github.com/nfc-tools/libnfc/blob/master/COPYING
 */
$ffi = FFI::cdef(<<<_
    /**
     * Connection string
     */
    typedef char nfc_connstring[1024];

    typedef enum {
      NOT_INTRUSIVE,
      INTRUSIVE,
      NOT_AVAILABLE,
    } scan_type_enum;

    struct nfc_user_defined_device {
      char name[256];
      nfc_connstring connstring;
      bool optional;
    };

    /**
     * @struct nfc_context
     * @brief NFC library context
     * Struct which contains internal options, references, pointers, etc. used by library
     */
    struct nfc_context {
      bool allow_autoscan;
      bool allow_intrusive_scan;
      uint32_t  log_level;
      struct nfc_user_defined_device user_defined_devices[4];
      unsigned int user_defined_device_count;
    };

    /**
     * NFC context
     */
    typedef struct nfc_context nfc_context;

    nfc_context *nfc_context_new(void);
    void nfc_context_free(nfc_context *context);

    /**
     * @struct nfc_device
     * @brief NFC device information
     */
    struct nfc_device {
      const nfc_context *context;
      const struct nfc_driver *driver;
      void *driver_data;
      void *chip_data;

      /** Device name string, including device wrapper firmware */
      char    name[256];
      /** Device connection string */
      nfc_connstring connstring;
      /** Is the CRC automaticly added, checked and removed from the frames */
      bool    bCrc;
      /** Does the chip handle parity bits, all parities are handled as data */
      bool    bPar;
      /** Should the chip handle frames encapsulation and chaining */
      bool    bEasyFraming;
      /** Should the chip try forever on select? */
      bool    bInfiniteSelect;
      /** Should the chip switch automatically activate ISO14443-4 when
          selecting tags supporting it? */
      bool    bAutoIso14443_4;
      /** Supported modulation encoded in a byte */
      uint8_t  btSupportByte;
      /** Last reported error */
      int     last_error;
    };

    /**
     * NFC device
     */
    typedef struct nfc_device nfc_device;

    /**
     * Properties
     */
    typedef enum {
      /**
       * Default command processing timeout
       * Property value's (duration) unit is ms and 0 means no timeout (infinite).
       * Default value is set by driver layer
       */
      NP_TIMEOUT_COMMAND,
      /**
       * Timeout between ATR_REQ and ATR_RES
       * When the device is in initiator mode, a target is considered as mute if no
       * valid ATR_RES is received within this timeout value.
       * Default value for this property is 103 ms on PN53x based devices.
       */
      NP_TIMEOUT_ATR,
      /**
       * Timeout value to give up reception from the target in case of no answer.
       * Default value for this property is 52 ms).
       */
      NP_TIMEOUT_COM,
      /** Let the PN53X chip handle the CRC bytes. This means that the chip appends
      * the CRC bytes to the frames that are transmitted. It will parse the last
      * bytes from received frames as incoming CRC bytes. They will be verified
      * against the used modulation and protocol. If an frame is expected with
      * incorrect CRC bytes this option should be disabled. Example frames where
      * this is useful are the ATQA and UID+BCC that are transmitted without CRC
      * bytes during the anti-collision phase of the ISO14443-A protocol. */
      NP_HANDLE_CRC,
      /** Parity bits in the network layer of ISO14443-A are by default generated and
       * validated in the PN53X chip. This is a very convenient feature. On certain
       * times though it is useful to get full control of the transmitted data. The
       * proprietary MIFARE Classic protocol uses for example custom (encrypted)
       * parity bits. For interoperability it is required to be completely
       * compatible, including the arbitrary parity bits. When this option is
       * disabled, the functions to communicating bits should be used. */
      NP_HANDLE_PARITY,
      /** This option can be used to enable or disable the electronic field of the
       * NFC device. */
      NP_ACTIVATE_FIELD,
      /** The internal CRYPTO1 co-processor can be used to transmit messages
       * encrypted. This option is automatically activated after a successful MIFARE
       * Classic authentication. */
      NP_ACTIVATE_CRYPTO1,
      /** The default configuration defines that the PN53X chip will try indefinitely
       * to invite a tag in the field to respond. This could be desired when it is
       * certain a tag will enter the field. On the other hand, when this is
       * uncertain, it will block the application. This option could best be compared
       * to the (NON)BLOCKING option used by (socket)network programming. */
      NP_INFINITE_SELECT,
      /** If this option is enabled, frames that carry less than 4 bits are allowed.
       * According to the standards these frames should normally be handles as
       * invalid frames. */
      NP_ACCEPT_INVALID_FRAMES,
      /** If the NFC device should only listen to frames, it could be useful to let
       * it gather multiple frames in a sequence. They will be stored in the internal
       * FIFO of the PN53X chip. This could be retrieved by using the receive data
       * functions. Note that if the chip runs out of bytes (FIFO = 64 bytes long),
       * it will overwrite the first received frames, so quick retrieving of the
       * received data is desirable. */
      NP_ACCEPT_MULTIPLE_FRAMES,
      /** This option can be used to enable or disable the auto-switching mode to
       * ISO14443-4 is device is compliant.
       * In initiator mode, it means that NFC chip will send RATS automatically when
       * select and it will automatically poll for ISO14443-4 card when ISO14443A is
       * requested.
       * In target mode, with a NFC chip compliant (ie. PN532), the chip will
       * emulate a 14443-4 PICC using hardware capability */
      NP_AUTO_ISO14443_4,
      /** Use automatic frames encapsulation and chaining. */
      NP_EASY_FRAMING,
      /** Force the chip to switch in ISO14443-A */
      NP_FORCE_ISO14443_A,
      /** Force the chip to switch in ISO14443-B */
      NP_FORCE_ISO14443_B,
      /** Force the chip to run at 106 kbps */
      NP_FORCE_SPEED_106,
    } nfc_property;

    // Compiler directive, set struct alignment to 1 uint8_t for compatibility
    #  pragma pack(1)

    /**
     * @enum nfc_dep_mode
     * @brief NFC D.E.P. (Data Exchange Protocol) active/passive mode
     */
    typedef enum {
      NDM_UNDEFINED = 0,
      NDM_PASSIVE,
      NDM_ACTIVE,
    } nfc_dep_mode;

    /**
     * @struct nfc_dep_info
     * @brief NFC target information in D.E.P. (Data Exchange Protocol) see ISO/IEC 18092 (NFCIP-1)
     */
    typedef struct {
      /** NFCID3 */
      uint8_t  abtNFCID3[10];
      /** DID */
      uint8_t  btDID;
      /** Supported send-bit rate */
      uint8_t  btBS;
      /** Supported receive-bit rate */
      uint8_t  btBR;
      /** Timeout value */
      uint8_t  btTO;
      /** PP Parameters */
      uint8_t  btPP;
      /** General Bytes */
      uint8_t  abtGB[48];
      size_t  szGB;
      /** DEP mode */
      nfc_dep_mode ndm;
    } nfc_dep_info;

    /**
     * @struct nfc_iso14443a_info
     * @brief NFC ISO14443A tag (MIFARE) information
     */
    typedef struct {
      uint8_t  abtAtqa[2];
      uint8_t  btSak;
      size_t  szUidLen;
      uint8_t  abtUid[10];
      size_t  szAtsLen;
      uint8_t  abtAts[254]; // Maximal theoretical ATS is FSD-2, FSD=256 for FSDI=8 in RATS
    } nfc_iso14443a_info;

    /**
     * @struct nfc_felica_info
     * @brief NFC FeLiCa tag information
     */
    typedef struct {
      size_t  szLen;
      uint8_t  btResCode;
      uint8_t  abtId[8];
      uint8_t  abtPad[8];
      uint8_t  abtSysCode[2];
    } nfc_felica_info;

    /**
     * @struct nfc_iso14443b_info
     * @brief NFC ISO14443B tag information
     */
    typedef struct {
      /** abtPupi store PUPI contained in ATQB (Answer To reQuest of type B) (see ISO14443-3) */
      uint8_t abtPupi[4];
      /** abtApplicationData store Application Data contained in ATQB (see ISO14443-3) */
      uint8_t abtApplicationData[4];
      /** abtProtocolInfo store Protocol Info contained in ATQB (see ISO14443-3) */
      uint8_t abtProtocolInfo[3];
      /** ui8CardIdentifier store CID (Card Identifier) attributted by PCD to the PICC */
      uint8_t ui8CardIdentifier;
    } nfc_iso14443b_info;

    /**
     * @struct nfc_iso14443bi_info
     * @brief NFC ISO14443B' tag information
     */
    typedef struct {
      /** DIV: 4 LSBytes of tag serial number */
      uint8_t abtDIV[4];
      /** Software version & type of REPGEN */
      uint8_t btVerLog;
      /** Config Byte, present if long REPGEN */
      uint8_t btConfig;
      /** ATR, if any */
      size_t szAtrLen;
      uint8_t  abtAtr[33];
    } nfc_iso14443bi_info;

    /**
     * @struct nfc_iso14443biclass_info
     * @brief NFC ISO14443BiClass tag information
     */
    typedef struct {
      uint8_t abtUID[8];
    } nfc_iso14443biclass_info;

    /**
     * @struct nfc_iso14443b2sr_info
     * @brief NFC ISO14443-2B ST SRx tag information
     */
    typedef struct {
      uint8_t abtUID[8];
    } nfc_iso14443b2sr_info;

    /**
     * @struct nfc_iso14443b2ct_info
     * @brief NFC ISO14443-2B ASK CTx tag information
     */
    typedef struct {
      uint8_t abtUID[4];
      uint8_t btProdCode;
      uint8_t btFabCode;
    } nfc_iso14443b2ct_info;

    /**
     * @struct nfc_jewel_info
     * @brief NFC Jewel tag information
     */
    typedef struct {
      uint8_t  btSensRes[2];
      uint8_t  btId[4];
    } nfc_jewel_info;

    /**
     * @struct nfc_barcode_info
     * @brief Thinfilm NFC Barcode information
     */
    typedef struct {
      size_t   szDataLen;
      uint8_t  abtData[32];
    } nfc_barcode_info;

    /**
     * @union nfc_target_info
     * @brief Union between all kind of tags information structures.
     */
    typedef union {
      nfc_iso14443a_info nai;
      nfc_felica_info nfi;
      nfc_iso14443b_info nbi;
      nfc_iso14443bi_info nii;
      nfc_iso14443b2sr_info nsi;
      nfc_iso14443b2ct_info nci;
      nfc_jewel_info nji;
      nfc_dep_info ndi;
      nfc_barcode_info nti; // "t" for Thinfilm, "b" already used
      nfc_iso14443biclass_info nhi; // hid iclass / picopass - nii already used
    } nfc_target_info;

    /**
     * @enum nfc_baud_rate
     * @brief NFC baud rate enumeration
     */
    typedef enum {
      NBR_UNDEFINED = 0,
      NBR_106,
      NBR_212,
      NBR_424,
      NBR_847,
    } nfc_baud_rate;

    /**
     * @enum nfc_modulation_type
     * @brief NFC modulation type enumeration
     */
    typedef enum {
      NMT_ISO14443A = 1,
      NMT_JEWEL,
      NMT_ISO14443B,
      NMT_ISO14443BI, // pre-ISO14443B aka ISO/IEC 14443 B' or Type B'
      NMT_ISO14443B2SR, // ISO14443-2B ST SRx
      NMT_ISO14443B2CT, // ISO14443-2B ASK CTx
      NMT_FELICA,
      NMT_DEP,
      NMT_BARCODE,    // Thinfilm NFC Barcode
      NMT_ISO14443BICLASS, // HID iClass 14443B mode
      NMT_END_ENUM = NMT_ISO14443BICLASS, // dummy for sizing - always should alias last
    } nfc_modulation_type;

    /**
     * @enum nfc_mode
     * @brief NFC mode type enumeration
     */
    typedef enum {
      N_TARGET,
      N_INITIATOR,
    } nfc_mode;

    /**
     * @struct nfc_modulation
     * @brief NFC modulation structure
     */
    typedef struct {
      nfc_modulation_type nmt;
      nfc_baud_rate nbr;
    } nfc_modulation;

    /**
     * @struct nfc_target
     * @brief NFC target structure
     */
    typedef struct {
      nfc_target_info nti;
      nfc_modulation nm;
    } nfc_target;

    void nfc_init(nfc_context **context);
    void nfc_exit(nfc_context *context);

    nfc_device* nfc_open(nfc_context *context, const nfc_connstring connstring);
    int nfc_initiator_init(nfc_device *pnd);
    const char* nfc_device_get_name(nfc_device *pnd);

    void nfc_perror(const nfc_device *pnd, const char *pcString);
    void nfc_close(nfc_device *pnd);

    size_t nfc_list_devices(nfc_context *context, nfc_connstring connstrings[], size_t connstrings_len);

    int nfc_initiator_select_passive_target(nfc_device *pnd, const nfc_modulation nm, const uint8_t *pbtInitData, const size_t szInitData, nfc_target *pnt);

    int nfc_initiator_poll_target(nfc_device *pnd,
                          const nfc_modulation *pnmModulations, const size_t szModulations,
                          const uint8_t uiPollNr, const uint8_t uiPeriod,
                          nfc_target *pnt);

   int str_nfc_target(char **buf, const nfc_target *pnt, bool verbose);
_, $libraryPath);

$context = $ffi->new('nfc_context *');
$ffi->nfc_init(FFI::addr($context));

$pnd = $ffi->nfc_open($context, null);

if ($pnd === null) {
    echo "Unable to open NFC device\n";

    $ffi->nfc_exit($context);

    exit(1);
}


if ($ffi->nfc_initiator_init($pnd) < 0) {

    $ffi->nfc_perror($pnd, "nfc_initiator_init");

    $ffi->nfc_exit($context);

    exit(1);
}

printf("NFC reader: %s opened\n", $ffi->nfc_device_get_name($pnd));

$nt = $ffi->new('nfc_target');

// nfc_modulation_type nmt;
//      nfc_baud_rate nbr;
$nmmMifare = $ffi->new('nfc_modulation[6]');

$nmmMifare[0]->nmt = $ffi->NMT_ISO14443A;
$nmmMifare[0]->nbr = $ffi->NBR_106;

$nmmMifare[1]->nmt = $ffi->NMT_ISO14443B;
$nmmMifare[1]->nbr = $ffi->NBR_106;

$nmmMifare[2]->nmt = $ffi->NMT_FELICA;
$nmmMifare[2]->nbr = $ffi->NBR_212;

$nmmMifare[3]->nmt = $ffi->NMT_FELICA;
$nmmMifare[3]->nbr = $ffi->NBR_424;

$nmmMifare[4]->nmt = $ffi->NMT_JEWEL;
$nmmMifare[4]->nbr = $ffi->NBR_106;

$nmmMifare[5]->nmt = $ffi->NMT_ISO14443BICLASS;
$nmmMifare[5]->nbr = $ffi->NBR_106;

$szModulations = 6;
$uiPollNr = 20;
$uiPeriod = 2;

if ($ffi->nfc_initiator_poll_target($pnd, $nmmMifare, $szModulations, $uiPollNr, $uiPeriod, FFI::addr($nt)) > 0) {
    $string = $ffi->new('char *');
    $ffi->str_nfc_target(FFI::addr($string), FFI::addr($nt), true);

    echo FFI::string($string) . "\n";
}

$ffi->nfc_close($pnd);

$ffi->nfc_exit($context);
