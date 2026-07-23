const ERROR_MESSAGES = {
  validation: "Data yang dikirim belum valid. Periksa kembali isian Anda.",
  unauthorized: "Sesi Anda tidak valid. Silakan masuk kembali.",
  forbidden: "Anda tidak memiliki akses untuk melakukan tindakan ini.",
  notFound: "Data yang diminta tidak ditemukan.",
  server: "Server sedang mengalami gangguan. Silakan coba lagi nanti.",
  network: "Tidak dapat terhubung ke server. Periksa koneksi internet Anda.",
  unknown: "Terjadi kesalahan. Silakan coba lagi.",
};

export const getApiError = (error) => {
  if (!error?.response) {
    return { type: "network", message: ERROR_MESSAGES.network };
  }

  const status = error.response.status;
  if (status === 422) {
    return {
      type: "validation",
      message: ERROR_MESSAGES.validation,
      errors: error.response.data?.errors || {},
    };
  }
  if (status === 401) return { type: "unauthorized", message: ERROR_MESSAGES.unauthorized };
  if (status === 403) return { type: "forbidden", message: ERROR_MESSAGES.forbidden };
  if (status === 404) return { type: "notFound", message: ERROR_MESSAGES.notFound };
  if (status >= 500) return { type: "server", message: ERROR_MESSAGES.server };

  return { type: "unknown", message: ERROR_MESSAGES.unknown };
};

export const getApiErrorMessage = (error) => getApiError(error).message;
