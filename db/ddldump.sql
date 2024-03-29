
SET SQL DIALECT 3; 

/* CREATE DATABASE '127.0.0.1:./db_quipu.fdb' PAGE_SIZE 4096 DEFAULT CHARACTER SET ISO8859_1; */


/*  Generators or sequences */
CREATE GENERATOR AS_GN_PERMISO_DIA;
CREATE GENERATOR AS_GN_VACACIONES;
CREATE GENERATOR CN_GN_ID_IMPUESTO;
CREATE GENERATOR SG_GN_ID_COPIA;

/* Domain definitions */
CREATE DOMAIN DM_BOOLEAN AS SMALLINT
         DEFAULT 1;
CREATE DOMAIN DM_COD AS VARCHAR(12);
CREATE DOMAIN DM_COD_CORTO AS VARCHAR(4);
CREATE DOMAIN DM_CREDITO AS VARCHAR(12);
CREATE DOMAIN DM_DIRE AS VARCHAR(200) CHARACTER SET ISO8859_1 COLLATE ES_ES;
CREATE DOMAIN DM_DNI AS VARCHAR(12);
CREATE DOMAIN DM_ESTADO_CIVIL AS CHAR(1)
         DEFAULT 'S';
CREATE DOMAIN DM_FONO AS VARCHAR(25);
CREATE DOMAIN DM_IMAGEN AS BLOB SUB_TYPE BLR SEGMENT SIZE 80;
CREATE DOMAIN DM_IP AS VARCHAR(40);
CREATE DOMAIN DM_MONEDA AS DECIMAL(10, 2);
CREATE DOMAIN DM_NOM AS VARCHAR(40) CHARACTER SET ISO8859_1 COLLATE ES_ES;
CREATE DOMAIN DM_NOM_LARGO AS VARCHAR(255) CHARACTER SET ISO8859_1 COLLATE ES_ES;
CREATE DOMAIN DM_TEXTO AS BLOB SUB_TYPE TEXT SEGMENT SIZE 80;
CREATE DOMAIN DM_TIPO_CLIENTE AS CHAR(1)
         DEFAULT 'N';
CREATE DOMAIN DM_TIPO_PAGO AS CHAR(1)
         DEFAULT 'E';
CREATE DOMAIN DM_USERNAME AS VARCHAR(32)
         DEFAULT USER;
CREATE DOMAIN DM_USERTIME AS TIMESTAMP
         DEFAULT 'NOW';
COMMIT WORK;

/* Table: AS_ASISTENCIA, Owner: SYSDBA */
CREATE TABLE AS_ASISTENCIA (FEC DATE NOT NULL,
        ID INTEGER NOT NULL,
        EMPLEADO_COD DM_COD,
        HOR TIME NOT NULL,
        PERMISO DM_BOOLEAN DEFAULT 0,
        TURNO_ID INTEGER,
        TURNO_MOV CHAR(1));

/* Table: AS_HGENERAL, Owner: SYSDBA */
CREATE TABLE AS_HGENERAL (COD DM_COD NOT NULL,
        TIPO_EMPLEADO_COD DM_COD_CORTO NOT NULL,
        FEC_INI DATE NOT NULL,
        FEC_FIN DATE NOT NULL);

/* Table: AS_HORARIO, Owner: SYSDBA */
CREATE TABLE AS_HORARIO (EMPLEADO_COD DM_COD NOT NULL,
        TURNO_ID INTEGER NOT NULL,
        ID INTEGER NOT NULL,
        DIA SMALLINT,
        HOR_ING TIME,
        HOR_SAL TIME,
        FEC_INI DATE NOT NULL,
        FEC_FIN DATE,
        TIP CHAR(1),
        HGENERAL_COD DM_COD);

/* Table: AS_PERMISO_DIA, Owner: SYSDBA */
CREATE TABLE AS_PERMISO_DIA (EMPLEADO_COD DM_COD NOT NULL,
        ID INTEGER NOT NULL,
        FEC DATE NOT NULL,
        MOT DM_TEXTO,
        HOR_DESDE TIME,
        HOR_HASTA TIME);

/* Table: AS_TURNO, Owner: SYSDBA */
CREATE TABLE AS_TURNO (ID INTEGER NOT NULL,
        NOM DM_NOM NOT NULL,
        APLICA_TARDANZA DM_BOOLEAN DEFAULT 1,
        APLICA_EXTRA DM_BOOLEAN DEFAULT 1);

/* Table: AS_VACACIONES, Owner: SYSDBA */
CREATE TABLE AS_VACACIONES (ID INTEGER NOT NULL,
        EMPLEADO_COD DM_COD NOT NULL,
        DES DATE NOT NULL,
        HAS DATE NOT NULL,
        ANIO CHAR(4) NOT NULL);

/* Table: CJ_BILLETAJE, Owner: SYSDBA */
CREATE TABLE CJ_BILLETAJE (AGENCIA_COD DM_COD_CORTO NOT NULL,
        CAJA_COD DM_COD_CORTO NOT NULL,
        FEC DATE NOT NULL,
        TIPO CHAR(1) DEFAULT 'I' NOT NULL,
        MONEDA_COD DM_COD_CORTO NOT NULL,
        TIPO_BILLETE_COD DM_COD_CORTO NOT NULL,
        CANT INTEGER,
        EST DM_BOOLEAN DEFAULT 1);

/* Table: CJ_CAJA, Owner: SYSDBA */
CREATE TABLE CJ_CAJA (AGENCIA_COD DM_COD_CORTO NOT NULL,
        COD DM_COD_CORTO NOT NULL,
        EMPLEADO_COD DM_COD NOT NULL,
        TIPO_CAJA_COD DM_COD_CORTO NOT NULL);

/* Table: CJ_DOC_PROV, Owner: SYSDBA */
CREATE TABLE CJ_DOC_PROV (AGENCIA_COD DM_COD_CORTO NOT NULL,
        ID INTEGER NOT NULL,
        NUM DM_COD NOT NULL,
        FEC DATE NOT NULL,
        CLIENTE_COD DM_COD,
        MONEDA_COD DM_COD_CORTO,
        MONTO DM_MONEDA,
        DET DM_TEXTO,
        USR DM_USERNAME,
        USRT DM_USERTIME);

/* Table: CJ_MOV_SENCILLO, Owner: SYSDBA */
CREATE TABLE CJ_MOV_SENCILLO (AGENCIA_COD DM_COD_CORTO NOT NULL,
        ID INTEGER NOT NULL,
        FEC TIMESTAMP DEFAULT 'NOW',
        MONTO DM_MONEDA NOT NULL,
        CAJA_AFUENTE DM_COD_CORTO NOT NULL,
        CAJA_FUENTE DM_COD_CORTO NOT NULL,
        CAJA_ADESTINO DM_COD_CORTO NOT NULL,
        CAJA_DESTINO DM_COD_CORTO NOT NULL,
        EST CHAR(1) DEFAULT 'E',
        USRT DM_USERTIME);

/* Table: CJ_TIPO_BILLETE, Owner: SYSDBA */
CREATE TABLE CJ_TIPO_BILLETE (MONEDA_COD DM_COD_CORTO NOT NULL,
        COD DM_COD_CORTO NOT NULL,
        VAL DM_MONEDA NOT NULL);

/* Table: CJ_TIPO_CAJA, Owner: SYSDBA */
CREATE TABLE CJ_TIPO_CAJA (COD DM_COD_CORTO NOT NULL,
        NOM DM_NOM NOT NULL);

/* Table: CN_IMPUESTO, Owner: SYSDBA */
CREATE TABLE CN_IMPUESTO (ID INTEGER NOT NULL,
        FEC_INI DATE NOT NULL,
        FEC_FIN DATE,
        IGV DM_MONEDA,
        RENTA DM_MONEDA,
        ITF DM_MONEDA,
        SEGURO DM_MONEDA,
        IGV_DEC NUMERIC(18, 6),
        RENTA_DEC NUMERIC(18, 6),
        SEGURO_DEC NUMERIC(18, 6),
        ITF_DEC NUMERIC(18, 6));

/* Table: CN_NUMLET, Owner: SYSDBA */
CREATE TABLE CN_NUMLET (COD VARCHAR(2) NOT NULL,
        LETRAS VARCHAR(40) CHARACTER SET ISO8859_1 COLLATE ES_ES);

/* Table: CR_ACTIVIDAD, Owner: SYSDBA */
CREATE TABLE CR_ACTIVIDAD (COD DM_COD NOT NULL,
        NOM DM_NOM NOT NULL,
        RUBRO_ACTIVIDAD_COD CHAR(1) NOT NULL);

/* Table: CR_AVAL, Owner: SYSDBA */
CREATE TABLE CR_AVAL (CREDITO_COD DM_COD NOT NULL,
        CLIENTE_COD DM_COD NOT NULL,
        PARENTESCO CHAR(1) DEFAULT 'C');

/* Table: CR_CIIU, Owner: SYSDBA */
CREATE TABLE CR_CIIU (COD DM_COD NOT NULL,
        NOM DM_NOM_LARGO NOT NULL);

/* Table: CR_CLIENTE, Owner: SYSDBA */
CREATE TABLE CR_CLIENTE (COD DM_COD NOT NULL,
        TIPO DM_TIPO_CLIENTE  DEFAULT 'N',
        PERSONA_COD DM_COD NOT NULL,
        RUC VARCHAR(11),
        CONYUGE DM_COD,
        NOM_COMERCIAL DM_NOM_LARGO,
        SIGLAS VARCHAR(10),
        DIRE DM_DIRE,
        LOCALIDAD_COD DM_COD,
        FONO1 DM_FONO,
        FONO2 DM_FONO,
        EST DM_BOOLEAN  DEFAULT 1,
        ESTADO_CIVIL CHAR(1)  DEFAULT 'S',
        NOM_LARGO DM_NOM_LARGO,
        DIR_NEGOCIO DM_DIRE,
        LOCALIDAD_NEGOCIO DM_COD,
        ACTIVIDAD_COD DM_COD);

/* Table: CR_CREDITO, Owner: SYSDBA */
CREATE TABLE CR_CREDITO (COD DM_COD NOT NULL,
        CLIENTE_COD DM_COD NOT NULL,
        AGENCIA_COD DM_COD_CORTO NOT NULL,
        FEC DATE DEFAULT CURRENT_DATE,
        FEC_RESOLUCION DATE,
        DESTINO_COD DM_COD_CORTO NOT NULL,
        LINEA_COD DM_COD NOT NULL,
        MODALIDAD_COD DM_COD NOT NULL,
        DOC_VIV DM_BOOLEAN  DEFAULT 0,
        DOC_PER DM_BOOLEAN  DEFAULT 0,
        DOC_NEG DM_BOOLEAN  DEFAULT 0,
        MONTO DM_MONEDA NOT NULL,
        PLAZO INTEGER NOT NULL,
        EST VARCHAR(1),
        USR DM_USERNAME,
        USRT DM_USERTIME,
        CIIU_COD DM_COD NOT NULL,
        AUTORIZADO_POR DM_USERNAME,
        AUTORIZADO_FEC DM_USERTIME,
        TI_MENSUAL DM_MONEDA NOT NULL,
        TI_MORA DM_MONEDA NOT NULL,
        TI_COMPENSA DM_MONEDA NOT NULL,
        OBS DM_TEXTO,
        IMPUESTO_ID INTEGER NOT NULL,
        DESEMBOLSADO_POR DM_USERNAME,
        DESEMBOLSADO_FEC DM_USERTIME,
        DESEMBOLSO_FEC DATE,
        COBRADOR_COD DM_COD,
        FEC_FIN DATE,
        ANALISTA_COD DM_COD,
        FEC_TER DATE);

/* Table: CR_CUOTA, Owner: SYSDBA */
CREATE TABLE CR_CUOTA (CREDITO_COD DM_COD NOT NULL,
        ID INTEGER NOT NULL,
        FEC DATE NOT NULL,
        CUOTA DM_MONEDA,
        SALDO_CAPITAL DM_MONEDA,
        CAPITAL DM_MONEDA,
        INTERES DM_MONEDA,
        ITF DM_MONEDA,
        OTROS DM_MONEDA,
        IGV DM_MONEDA,
        TOTAL DM_MONEDA NOT NULL,
        DIA VARCHAR(10) CHARACTER SET ISO8859_1 COLLATE ES_ES,
        EST CHAR(1) DEFAULT 'A',
        TOTAL_CANCELADO DM_MONEDA,
        TOTAL_ITF DM_MONEDA,
        SEGURO DM_MONEDA,
        MORA DM_MONEDA,
        FEC_MORA DATE,
        DIF DM_MONEDA,
        TOTAL_CONS DM_MONEDA);

/* Table: CR_DESTINO, Owner: SYSDBA */
CREATE TABLE CR_DESTINO (COD DM_COD_CORTO NOT NULL,
        NOM DM_NOM NOT NULL);

/* Table: CR_FERIADO, Owner: SYSDBA */
CREATE TABLE CR_FERIADO (FEC DATE NOT NULL);

/* Table: CR_LINEA, Owner: SYSDBA */
CREATE TABLE CR_LINEA (COD DM_COD NOT NULL,
        NOM DM_NOM_LARGO NOT NULL,
        TI_MENSUAL DM_MONEDA,
        GA DM_MONEDA,
        MONEDA_COD DM_COD_CORTO,
        MONTO_MIN DM_MONEDA  DEFAULT 0.00,
        MONTO_MAX DM_MONEDA  DEFAULT 0.00,
        CUOTA_MIN INTEGER DEFAULT 1,
        CUOTA_MAX INTEGER DEFAULT 1,
        TI_MORA DM_MONEDA,
        TI_COMPENSA DM_MONEDA,
        TI_ANUAL DOUBLE PRECISION,
        TI_DIARIO DOUBLE PRECISION,
        EXTRA_JUDICIAL DM_BOOLEAN DEFAULT 0);

/* Table: CR_MODALIDAD, Owner: SYSDBA */
CREATE TABLE CR_MODALIDAD (COD DM_COD NOT NULL,
        NOM DM_NOM NOT NULL);

/* Table: CR_MONEDA, Owner: SYSDBA */
CREATE TABLE CR_MONEDA (COD DM_COD_CORTO NOT NULL,
        NOM DM_NOM NOT NULL,
        ABREV VARCHAR(4) NOT NULL);

/* Table: CR_PAGO, Owner: SYSDBA */
CREATE TABLE CR_PAGO (CREDITO_COD DM_COD NOT NULL,
        CUOTA_ID INTEGER NOT NULL,
        ID INTEGER NOT NULL,
        CAPITAL DM_MONEDA,
        INTERES DM_MONEDA,
        IGV DM_MONEDA,
        MORA DM_MONEDA,
        ITF DM_MONEDA);

/* Table: CR_RECIBO, Owner: SYSDBA */
CREATE TABLE CR_RECIBO (COD DM_COD NOT NULL,
        CREDITO_COD DM_CREDITO,
        FEC DATE DEFAULT CURRENT_DATE,
        ITF DM_MONEDA NOT NULL,
        A_CUENTA DM_MONEDA NOT NULL,
        USR DM_USERNAME,
        USRT DM_USERTIME,
        MONTO DM_MONEDA NOT NULL,
        A_CUENTA_SIG DM_MONEDA,
        EST DM_BOOLEAN,
        CUOTA_PENDIENTE INTEGER,
        DET DM_TEXTO,
        MONEDA_COD DM_COD_CORTO,
        TIPO CHAR(1) DEFAULT 'C',
        IGV DM_MONEDA,
        SUB_TOTAL NUMERIC(18, 2),
        PERSONA_COD VARCHAR(12),
        AFECTO_IGV DM_BOOLEAN DEFAULT 1);

/* Table: CR_RECIBO_ANULAR, Owner: SYSDBA */
CREATE TABLE CR_RECIBO_ANULAR (RECIBO_COD DM_COD NOT NULL,
        OBS DM_TEXTO,
        USR DM_USERNAME,
        USRT DM_USERTIME);

/* Table: CR_RECIBO_CUOTA, Owner: SYSDBA */
CREATE TABLE CR_RECIBO_CUOTA (RECIBO_COD VARCHAR(12) NOT NULL,
        ID INTEGER NOT NULL,
        CREDITO_COD VARCHAR(12) NOT NULL,
        CUOTA_ID INTEGER NOT NULL,
        CAPITAL DM_MONEDA,
        INTERES DM_MONEDA,
        IGV DM_MONEDA,
        MORA DM_MONEDA,
        USR DM_USERNAME,
        UST DM_USERTIME,
        SEGURO DM_MONEDA,
        OTROS DM_MONEDA,
        ITF DM_MONEDA);

/* Table: CR_RUBRO_ACTIVIDAD, Owner: SYSDBA */
CREATE TABLE CR_RUBRO_ACTIVIDAD (COD CHAR(1) NOT NULL,
        NOM DM_NOM NOT NULL);

/* Table: CR_SOLICITUD, Owner: SYSDBA */
CREATE TABLE CR_SOLICITUD (CREDITO_COD DM_COD NOT NULL,
        CRED_1 DM_COD,
        CRED_2 DM_COD,
        CUOTA_PAG_1 INTEGER,
        CUOTA_PAG_2 INTEGER,
        CUOTA_ADE_1 INTEGER,
        CUOTA_ADE_2 INTEGER,
        VEN_1 INTEGER,
        VEN_2 INTEGER,
        FEC_ULTIMO_1 DATE,
        FEC_ULTIMO_2 DATE,
        INVERSION DM_TEXTO,
        DEP_ADULTO SMALLINT DEFAULT 0,
        DEP_MENOR SMALLINT DEFAULT 0,
        VAL_INMUEBLE DM_MONEDA,
        VAL_MUEBLE_CLIENTE DM_MONEDA,
        VAL_MUEBLE_NEGOCIO DM_MONEDA,
        SAL_TOT_1 DM_MONEDA,
        SAL_TOT_2 DM_MONEDA,
        ACREEDOR DM_TEXTO,
        CAP_TRAB_EFECTIVO DM_MONEDA,
        CAP_TRAB_MERCA DM_MONEDA,
        VENT_ING DM_MONEDA,
        OTROS_ING DM_MONEDA,
        COSTO_INSUMO DM_MONEDA,
        GASTO_FAMILIAR DM_MONEDA,
        GASTO_NEGOCIO DM_MONEDA,
        PAGO_PROM_1 DM_MONEDA,
        PAGO_PROM_2 DM_MONEDA,
        PAGO_PROM_ACREEDOR DM_MONEDA,
        GASTO_PERSONAL DM_MONEDA,
        SALDO DM_MONEDA,
        OBS DM_MONEDA,
        INFORME_RIESGO DM_TEXTO,
        OPINION_ANALISTA DM_TEXTO,
        TOT_1 INTEGER,
        TOT_2 INTEGER,
        MORA_1 INTEGER,
        MORA_2 INTEGER,
        OPINION_SBS DM_COD_CORTO);

/* Table: CR_SOLICITUD_DETALLE, Owner: SYSDBA */
CREATE TABLE CR_SOLICITUD_DETALLE (CREDITO_COD VARCHAR(12) NOT NULL,
        COD VARCHAR(12) NOT NULL,
        CUOTA_TOTAL INTEGER,
        CUOTA_MORA INTEGER,
        CUOTA_ADELANTO INTEGER,
        CUOTA_ADELANTO_TOTAL INTEGER,
        CUOTA_VENCIDA INTEGER,
        FEC_ULTIMO DATE);

/* Table: CR_TARIFA, Owner: SYSDBA */
CREATE TABLE CR_TARIFA (CLIENTE_COD DM_COD NOT NULL,
        TI_MORA DM_MONEDA  DEFAULT 0.00,
        TI_COMPENSA DM_MONEDA  DEFAULT 0.00,
        TI_MENSUAL DM_MONEDA  DEFAULT 0.00);

/* Table: CR_TEMP_CRONO, Owner: SYSDBA */
CREATE TABLE CR_TEMP_CRONO (ID INTEGER NOT NULL,
        FEC DATE NOT NULL,
        CUOTA DM_MONEDA,
        SALDO_CAPITAL DM_MONEDA,
        CAPITAL DM_MONEDA,
        INTERES DM_MONEDA,
        ITF DM_MONEDA,
        OTROS DM_MONEDA,
        IGV DM_MONEDA,
        TOTAL DM_MONEDA NOT NULL,
        DIA VARCHAR(10) CHARACTER SET ISO8859_1 COLLATE ES_ES,
        TOTAL_CANCELADO DM_MONEDA,
        TOTAL_ITF DM_MONEDA,
        SEGURO DM_MONEDA,
        DIF DM_MONEDA);

/* Table: CR_TIPO_CREDITO, Owner: SYSDBA */
CREATE TABLE CR_TIPO_CREDITO (COD DM_COD_CORTO NOT NULL,
        NOM DM_NOM NOT NULL);

/* Table: CR_TIPO_PRESTAMO, Owner: SYSDBA */
CREATE TABLE CR_TIPO_PRESTAMO (COD DM_COD_CORTO NOT NULL,
        NOM DM_NOM NOT NULL);

/* Table: LG_AUDITOR, Owner: SYSDBA */
CREATE TABLE LG_AUDITOR (FEC DATE NOT NULL,
        ID INTEGER NOT NULL,
        OPER CHAR(1) NOT NULL,
        TABLA VARCHAR(32),
        VAL_ANT DM_TEXTO,
        VAL_POST DM_TEXTO,
        URL DM_TEXTO,
        USR DM_USERNAME,
        USRT DM_USERTIME,
        MAQ VARCHAR(20));

/* Table: MO_CONCEPTO, Owner: SYSDBA */
CREATE TABLE MO_CONCEPTO (CREDITO_COD DM_COD NOT NULL,
        TIPO_COBRO_COD DM_COD_CORTO NOT NULL,
        MONTO DM_MONEDA);

/* Table: MO_MORA, Owner: SYSDBA */
CREATE TABLE MO_MORA (CREDITO_COD DM_COD NOT NULL,
        MONTO DM_MONEDA NOT NULL,
        PLAZO INTEGER NOT NULL,
        FEC_INI DATE,
        FEC_EMISION DATE);

/* Table: MO_TIPO_COBRO, Owner: SYSDBA */
CREATE TABLE MO_TIPO_COBRO (COD DM_COD_CORTO NOT NULL,
        NOM DM_NOM NOT NULL);

/* Table: QP_AGENCIA, Owner: SYSDBA */
CREATE TABLE QP_AGENCIA (COD DM_COD_CORTO NOT NULL,
        NOM DM_NOM_LARGO NOT NULL,
        DIRE DM_DIRE NOT NULL,
        FONO1 DM_FONO NOT NULL,
        FONO2 DM_FONO);

/* Table: QP_EMPLEADO, Owner: SYSDBA */
CREATE TABLE QP_EMPLEADO (COD DM_COD NOT NULL,
        TIPO_EMPLEADO_COD DM_COD_CORTO NOT NULL,
        AGENCIA_COD DM_COD_CORTO NOT NULL,
        FEC_INI DATE,
        FEC_TER DATE,
        EST DM_BOOLEAN);

/* Table: QP_EMPRESA, Owner: SYSDBA */
CREATE TABLE QP_EMPRESA (COD DM_COD NOT NULL,
        NOM DM_NOM_LARGO NOT NULL,
        DIRE DM_DIRE NOT NULL,
        FONO1 DM_FONO NOT NULL,
        FONO2 DM_FONO,
        FEC_INI DATE,
        INFOCORP_COD VARCHAR(6));

/* Table: QP_ESTADO_CIVIL, Owner: SYSDBA */
CREATE TABLE QP_ESTADO_CIVIL (COD CHAR(1) NOT NULL,
        NOM DM_NOM NOT NULL);

/* Table: QP_GENERADOR, Owner: SYSDBA */
CREATE TABLE QP_GENERADOR (COD DM_COD NOT NULL,
        VAL INTEGER NOT NULL);

/* Table: QP_LOCALIDAD, Owner: SYSDBA */
CREATE TABLE QP_LOCALIDAD (COD DM_COD NOT NULL,
        NOM DM_NOM_LARGO NOT NULL,
        PADRE DM_COD,
        NIVEL SMALLINT DEFAULT 1);

/* Table: QP_MENU_ITEM, Owner: SYSDBA */
CREATE TABLE QP_MENU_ITEM (COD DM_COD NOT NULL,
        NOM VARCHAR(100) CHARACTER SET ISO8859_1 COLLATE ES_ES,
        URL VARCHAR(300),
        PADRE_COD DM_COD,
        SEP DM_BOOLEAN DEFAULT 0);

/* Table: QP_MENU_ROL, Owner: SYSDBA */
CREATE TABLE QP_MENU_ROL (MENU_ITEM_COD DM_COD NOT NULL,
        ROL_COD VARCHAR(32) NOT NULL,
        EST DM_BOOLEAN DEFAULT 0);

/* Table: QP_PERSONA, Owner: SYSDBA */
CREATE TABLE QP_PERSONA (COD DM_COD NOT NULL,
        NOM DM_NOM NOT NULL,
        APE_PAT DM_NOM NOT NULL,
        APE_MAT DM_NOM NOT NULL,
        DIRE DM_DIRE NOT NULL,
        DNI DM_DNI,
        FONO1 DM_FONO,
        NOM_LARGO VARCHAR(0),
        FEC_NAC DATE,
        DNI_VEN DATE,
        TIPO_DOC_COD DM_COD_CORTO,
        SEXO CHAR(1) DEFAULT 'M');

/* Table: QP_TIPO_DOC, Owner: SYSDBA */
CREATE TABLE QP_TIPO_DOC (COD DM_COD_CORTO NOT NULL,
        NOM DM_NOM NOT NULL,
        ABREV VARCHAR(6) NOT NULL);

/* Table: QP_TIPO_EMPLEADO, Owner: SYSDBA */
CREATE TABLE QP_TIPO_EMPLEADO (COD DM_COD_CORTO NOT NULL,
        NOM DM_NOM NOT NULL);

/* Table: QP_TOOLBAR, Owner: SYSDBA */
CREATE TABLE QP_TOOLBAR (USUARIO_COD DM_USERNAME NOT NULL,
        MENU_ITEM_COD DM_COD NOT NULL,
        IMG VARCHAR(32) NOT NULL);

/* Table: SB_OPINION, Owner: SYSDBA */
CREATE TABLE SB_OPINION (COD DM_COD_CORTO NOT NULL,
        NOM DM_NOM NOT NULL);

/* Table: SG_COPIA, Owner: SYSDBA */
CREATE TABLE SG_COPIA (ID INTEGER NOT NULL,
        FEC TIMESTAMP DEFAULT 'NOW',
        NOM VARCHAR(32) CHARACTER SET ISO8859_1 COLLATE ES_ES,
        USR DM_USERNAME,
        USRT DM_USERTIME);

/* Table: SG_ROL, Owner: SYSDBA */
CREATE TABLE SG_ROL (COD VARCHAR(32) NOT NULL,
        NOM DM_NOM NOT NULL);

/* Table: SG_USUARIO, Owner: SYSDBA */
CREATE TABLE SG_USUARIO (COD DM_COD NOT NULL,
        USR VARCHAR(32) NOT NULL,
        PASS VARCHAR(32) NOT NULL,
        ROL VARCHAR(32) NOT NULL,
        FEC_PASS DATE DEFAULT CURRENT_DATE,
        DIAS_PASS INTEGER,
        IP DM_IP);

/*  External Function declarations */
DECLARE EXTERNAL FUNCTION DOW
TIMESTAMP, VARCHAR(15) CHARACTER SET ISO8859_1
RETURNS PARAMETER 2
ENTRY_POINT 'DOW' MODULE_NAME 'fbudf';

DECLARE EXTERNAL FUNCTION DPOWER
DOUBLE PRECISION BY DESCRIPTOR, DOUBLE PRECISION BY DESCRIPTOR, DOUBLE PRECISION BY DESCRIPTOR
RETURNS PARAMETER 3
ENTRY_POINT 'power' MODULE_NAME 'fbudf';

DECLARE EXTERNAL FUNCTION F_DAYOFWEEK
TIMESTAMP
RETURNS INTEGER FREE_IT
ENTRY_POINT 'dayofweek' MODULE_NAME 'FreeAdhocUDF';

DECLARE EXTERNAL FUNCTION "LN"
DOUBLE PRECISION
RETURNS DOUBLE PRECISION BY VALUE 
ENTRY_POINT 'IB_UDF_ln' MODULE_NAME 'ib_udf';

DECLARE EXTERNAL FUNCTION "LOG"
DOUBLE PRECISION, DOUBLE PRECISION
RETURNS DOUBLE PRECISION BY VALUE 
ENTRY_POINT 'IB_UDF_log' MODULE_NAME 'ib_udf';

DECLARE EXTERNAL FUNCTION "LOG10"
DOUBLE PRECISION
RETURNS DOUBLE PRECISION BY VALUE 
ENTRY_POINT 'IB_UDF_log10' MODULE_NAME 'ib_udf';

DECLARE EXTERNAL FUNCTION "LPAD"
CSTRING(255) CHARACTER SET ISO8859_1, INTEGER, CSTRING(1) CHARACTER SET ISO8859_1
RETURNS CSTRING(255) CHARACTER SET ISO8859_1 
ENTRY_POINT 'IB_UDF_lpad' MODULE_NAME 'ib_udf';

DECLARE EXTERNAL FUNCTION "ROUND"
INTEGER BY DESCRIPTOR, INTEGER BY DESCRIPTOR
RETURNS PARAMETER 2
ENTRY_POINT 'fbround' MODULE_NAME 'fbudf';

DECLARE EXTERNAL FUNCTION SDOW
TIMESTAMP, VARCHAR(5) CHARACTER SET ISO8859_1
RETURNS PARAMETER 2
ENTRY_POINT 'SDOW' MODULE_NAME 'fbudf';

DECLARE EXTERNAL FUNCTION "SQRT"
DOUBLE PRECISION
RETURNS DOUBLE PRECISION BY VALUE 
ENTRY_POINT 'IB_UDF_sqrt' MODULE_NAME 'ib_udf';

DECLARE EXTERNAL FUNCTION TRUNCATE
INTEGER BY DESCRIPTOR, INTEGER BY DESCRIPTOR
RETURNS PARAMETER 2
ENTRY_POINT 'fbtruncate' MODULE_NAME 'fbudf';

