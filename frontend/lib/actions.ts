import qs from 'query-string';
import { apiUrl } from '@/config/appConfig';

interface GetParams {
    page?: number;

}

interface UrlQueryParams {
    params: string;
    key?: string;
    value?: string | null
    keysToRemove?: string[];
};

export const formUrlQuery = ({ params, key, value, keysToRemove }: UrlQueryParams) => {
    const currentUrl = qs.parse(params);

    if (keysToRemove) {
        keysToRemove.forEach((keyToRemove) => {
            delete currentUrl[keyToRemove];
        })
    } else if (key && value) {
        currentUrl[key] = value;
    }

    const stringifyUrl = qs.stringifyUrl(
        { url: window.location.pathname, query: currentUrl },
        { skipNull: true }
    )

    return stringifyUrl;
}

export const getFonts = async (params: GetParams) => {
    const { page } = params;
    let fonts: any = [];
    try {
        fonts = await fetch(`${apiUrl}/font?page=${page}`);
        const result = await fonts.json();
        return result.fonts;
    } catch (error) {
        return fonts;
    }
}

export const fontUpload = async (file: File) => {
    try {
        const formdata = new FormData();
        formdata.append("font", file);

        const requestOptions: any = {
            method: "POST",
            body: formdata,
            redirect: "follow"
        };
        const fonts_upload = await fetch(`${apiUrl}/font/upload`, requestOptions);
        const result = await fonts_upload.json();
        return result;
    } catch (error) {
        return {
            'success': false,
            'message': error,
        };
    }
}

export const fontDelete = async (id: number) => {
    try {
        const requestOptions: any = {
            method: "DELETE",
            redirect: "follow"
        };

        const font_delete = await fetch(`${apiUrl}/font/${id}`, requestOptions);
        const result = await font_delete.json();
        return result;
    } catch (error) {
        return {
            'success': false,
            'message': error,
        };
    }
}


