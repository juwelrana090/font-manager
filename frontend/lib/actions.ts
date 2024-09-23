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
            'status': false,
            'message': error,
        };
    }
}


