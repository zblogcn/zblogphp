import {AiEditor} from './index.js';
const textarea = document.getElementById('editor_content');
const aiEditor = new AiEditor({
    element: "#aiEditor",
    toolbarKeys: ["undo", "redo", "brush", "eraser",
        "|", "heading", "font-family", "font-size",
        "|", "bold", "italic", "underline", "strike", "link", "code", "subscript", "superscript", "hr", "todo", "emoji",
        "|", "highlight", "font-color",
        "|", "align", "line-height",
        "|", "bullet-list", "ordered-list", "indent-decrease", "indent-increase", "break",
        "|", "image", "video", "attachment", "quote", "code-block", "table",
        "|", "source-code", "printer", "fullscreen", "ai"
    ],
    placeholder: "开始您的创作...",
    onChange:(aiEditor)=>{
        const textarea = document.getElementById('editor_content');
        textarea.value = aiEditor.getHtml();
    },
    content: textarea.value,
    name: "Content",
    pasteAsText: true,
    textSelectionBubbleMenu: {
        enable: false,
    },

    image: {
        allowBase64: false,
        defaultSize: 350,
        uploadUrl: ajaxurl+'toyean_upload',
        uploadFormName: "image", //上传时的文件表单名称
        uploadHeaders: {
            "jwt": "xxxxx",
            "other": "xxxx",
        },
        uploader: (file, uploadUrl, headers, formName) => {
            const formData = new FormData();
            formData.append(formName, file);
            return new Promise((resolve, reject) => {
                fetch(uploadUrl, {
                    method: "post",
                    headers: {'Accept': 'application/json', ...headers},
                    body: formData,
                }).then((resp) => resp.json())
                    .then(json => {
                        if(json.msg){
                            toast(json.msg,2000,'center');
                        }
                        resolve(json);
                    }).catch((error) => {
                    reject(error);
                })
            });
        },
        uploaderEvent: {
            onUploadBefore: (file, uploadUrl, headers) => {
                //监听图片上传之前，此方法可以不用回任何内容，但若返回 false，则终止上传
            },
            onSuccess: (file, response) => {
                //监听图片上传成功
                //图片上传成功后，编辑器会将图片插入到光标所在位置，并在图片外层包裹一个p标签
                console.log(response.url);

            },
            onFailed: (file, response) => {
                //监听图片上传失败，或者返回的 json 信息不正确
            },
            onError: (file, error) => {
                //监听图片上传错误，比如网络超时等
            },
        },
        bubbleMenuItems: ["AlignLeft", "AlignCenter", "AlignRight", "delete"]
    },


    video: {
        uploadUrl: ajaxurl+'toyean_upload',
        uploadFormName: "video", //上传时的文件表单名称
        uploadHeaders: {
            "jwt": "xxxxx",
            "other": "xxxx",
        },
        uploader: (file, uploadUrl, headers, formName) => {
            //可自定义视频上传逻辑
            const formData = new FormData();
            formData.append(formName, file);
            return new Promise((resolve, reject) => {
                fetch(uploadUrl, {
                    method: "post",
                    headers: {'Accept': 'application/json', ...headers},
                    body: formData,
                }).then((resp) => resp.json()).then(json => {
                    if(json.msg){
                        toast(json.msg,2000,'center');
                    }
                    resolve(json);
                    }).catch((error) => {
                    reject(error);
                });
            });
        },
        uploaderEvent: {
            onUploadBefore: (file, uploadUrl, headers) => {
                //监听视频上传之前，此方法可以不用回任何内容，但若返回 false，则终止上传
            },
            onSuccess: (file, response) => {
                //监听视频上传成功
                //注意：
                // 1、如果此方法返回 false，则视频不会被插入到编辑器
                // 2、可以在这里返回一个新的 json 给编辑器
            },
            onFailed: (file, response) => {
                //监听视频上传失败，或者返回的 json 信息不正确
            },
            onError: (file, error) => {
                //监听视频上传错误，比如网络超时等
            },
        }
    },

    onMentionQuery: (query) => {
        return mentions.filter(item => item.toLowerCase().startsWith(query.toLowerCase())).slice(0, 5)
    },
});