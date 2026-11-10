const isBrowser = typeof window !== 'undefined' && window.location;
const isSubdir = isBrowser && window.location.pathname.startsWith('/evoHRIS_v2.1');
const originUrl = isBrowser ? window.location.origin : 'http://127.0.0.1:8099';
const baseRoot = isSubdir ? `${originUrl}/evoHRIS_v2.1` : originUrl;

const config = {
  "environment": "development",
  "debug": true,
  "trace_info": false,
  "auth_key": "app202101281103",
  "base_url": baseRoot,
  "api_url": `${baseRoot}/api`,
  "simrs": "http://192.168.100.246/migrasi/evoMedis/index.php",
  "pacs": "http://10.100.1.43/app/viewer",
  "dokumen_rm": "http://192.168.100.246/migrasi/dokumen_rm",
  "remedis": "http://192.168.100.246/migrasi/remedis/api",
  "evo_inap": "http://192.168.100.246/migrasi/evoMedis/api",
  "tipe_resep": "A"
};
const environment = config.environment;
const path = config.path;
