import qs from 'query-string';
import { apiUrl } from '@/config/appConfig';

interface GetParams {
    page?: number;

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

export const fontDelete = async (id: string) => {
    try {
        const font_delete = await fetch(`${apiUrl}/font/${id}`, {
            method: 'DELETE'
        });
        const result = await font_delete.json();
        return result;
    } catch (error) {
        return {
            'status': false,
            'message': error,
        };
    }
}