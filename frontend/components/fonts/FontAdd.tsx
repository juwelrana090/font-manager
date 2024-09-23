'use client';

import React, { DragEvent, useEffect, useRef, useState } from "react";
import { useRouter } from 'next/navigation';
import { Button, Modal } from 'antd';

// Redux
import { useAppDispatch } from "@/redux/hooks";

import { fontUpload, getFonts } from "@/lib/actions";
import { setFontList } from "@/redux/slices/fontSlice";
import Swal from "sweetalert2";

const FontAdd = () => {
    const router = useRouter();
    const dispatch = useAppDispatch();

    const [open, setOpen] = useState(false);
    const [dragIsOver, setDragIsOver] = useState(false);
    const [errorMessage, setErrorMessage] = useState("");

    const validate = (values: { type: string; size: number }) => {
        const allowImageSize = 1024000;
        const allowedTypes = ["ttf"];

        let errors: any = {};

        const setError = (message: string) => {
            setErrorMessage(message);
        };

        if (!allowedTypes.includes(values.type)) {
            setError("File type not allowed");
        }

        if (values.size > allowImageSize) {
            setError("File size is too large");
        }

        return errors;
    };

    const handleDragOver = (event: DragEvent<HTMLDivElement>) => {
        event.preventDefault();
        setDragIsOver(true);
    };

    const handleDragLeave = (event: DragEvent<HTMLDivElement>) => {
        event.preventDefault();
        setDragIsOver(false);
    };

    const handleFontUpload = async (file: File) => {
        const font_upload = await fontUpload(file);
        if (font_upload?.success) {
            Swal.fire({
                title: "Font Uploaded!",
                text: font_upload.message,
                icon: "success"
            });
            const fonts = await getFonts({
                page: 1
            });

            if (fonts.data.length > 0) {
                dispatch(setFontList(fonts));
            }

            setOpen(false);
        } else {
            Swal.fire({
                title: "Error!",
                text: font_upload.message,
                icon: "error"
            });
        }
    };

    const handleDrop = (event: DragEvent<HTMLDivElement>) => {
        event.preventDefault();

        setDragIsOver(false);

        const droppedFiles = Array.from(event.dataTransfer.files);
        const file = droppedFiles[0];
        const fileArray = file?.name.split('.');
        const type = fileArray[fileArray.length - 1].toLowerCase();
        const size = file?.size;

        const errors = validate({
            type: type,
            size: size,
        });

        if (!errors?.type?.length && !errors?.size?.length) {
            setErrorMessage("");
            handleFontUpload(file);
        }
    };

    const handleUpload = (e: { target: { [x: string]: any; name: any; value: any } }) => {
        setDragIsOver(false);

        const file = e?.target?.files[0];
        const fileArray = file?.name.split('.');
        const type = fileArray[fileArray.length - 1].toLowerCase();
        const size = file?.size;

        const errors = validate({
            type: type,
            size: size,
        });

        if (!errors?.type?.length && !errors?.size?.length) {
            setErrorMessage("");
            handleFontUpload(file);
        }
    };

    return (
        <div className='w-full flex justify-end items-center'>
            <Button type="primary" onClick={() => setOpen(true)}>
                Add Font
            </Button>
            <Modal
                title={null}
                centered
                open={open}
                closable={false}
                onOk={() => setOpen(false)}
                onCancel={() => setOpen(false)}
                width={1000}
                footer={null}
            >
                <label
                    htmlFor="upload"
                    className={`w-full h-[200px] flex justify-center items-center p-2 bg-white border-dashed border-2 rounded-3xl shadow cursor-pointer ${dragIsOver ? "border-gray-800" : "border-gray-300"}`}
                    //@ts-ignore
                    onDragOver={(e: DragEvent<HTMLDivElement>) =>
                        handleDragOver(e)
                    }
                    //@ts-ignore
                    onDragLeave={(e: DragEvent<HTMLDivElement>) =>
                        handleDragLeave(e)
                    }
                    //@ts-ignore
                    onDrop={(e: DragEvent<HTMLDivElement>) =>
                        handleDrop(e)
                    }
                >
                    <div className="w-full">
                        <div className="w-full text-center">
                            {
                                errorMessage.length > 0 ?
                                    <p className="text-red-500 text-[18px] font-semibold">
                                        {
                                            errorMessage
                                        }
                                    </p> : null
                            }

                            <p className="w-full text-gray-500 text-[16px] font-normal font-serif">
                                <span className="text-gray-700 font-medium">Click to upload </span> or drag and drop
                            </p>
                            <p className="w-full text-gray-500 text-[16px] font-normal font-serif">
                                Only TTF File Allowed
                            </p>
                        </div>
                    </div>
                    <input
                        type="file"
                        id="upload"
                        name="file"
                        className="hidden"
                        onChange={(e) =>
                            handleUpload(e)
                        }
                    />
                </label>
            </Modal>
        </div>
    )
}

export default FontAdd;
