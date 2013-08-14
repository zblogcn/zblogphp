using System;
using System.Collections;
using System.Collections.Generic;
using System.Data;
using System.Diagnostics;

namespace ASP2PHP
{
    partial class frmMain
    {
        /// <summary>
        /// 必需的设计器变量。
        /// </summary>
        private System.ComponentModel.IContainer components = null;
        public System.Windows.Forms.ToolTip ToolTip1;
        public System.Windows.Forms.ListBox lstLog;
        public System.Windows.Forms.Button cmdOpen;
        public System.Windows.Forms.Button cmdBrowse;
        public System.Windows.Forms.TextBox txtPath;
        public System.Windows.Forms.Label lblNote;
        public System.Windows.Forms.Label lblFolder;
        internal System.Windows.Forms.FolderBrowserDialog fbdDialog;

        /// <summary>
        /// 清理所有正在使用的资源。
        /// </summary>
        /// <param name="disposing">如果应释放托管资源，为 true；否则为 false。</param>
        protected override void Dispose(bool disposing)
        {
            if (disposing && (components != null))
            {
                components.Dispose();
            }
            base.Dispose(disposing);
        }

        #region Windows 窗体设计器生成的代码

        /// <summary>
        /// 设计器支持所需的方法 - 不要
        /// 使用代码编辑器修改此方法的内容。
        /// </summary>
        private void InitializeComponent()
        {
            this.components = new System.ComponentModel.Container();
            this.ToolTip1 = new System.Windows.Forms.ToolTip(this.components);
            this.lstLog = new System.Windows.Forms.ListBox();
            this.cmdOpen = new System.Windows.Forms.Button();
            this.cmdBrowse = new System.Windows.Forms.Button();
            this.txtPath = new System.Windows.Forms.TextBox();
            this.lblNote = new System.Windows.Forms.Label();
            this.lblFolder = new System.Windows.Forms.Label();
            this.fbdDialog = new System.Windows.Forms.FolderBrowserDialog();
            this.SuspendLayout();
            // 
            // lstLog
            // 
            this.lstLog.BackColor = System.Drawing.SystemColors.Window;
            this.lstLog.Cursor = System.Windows.Forms.Cursors.Default;
            this.lstLog.ForeColor = System.Drawing.SystemColors.WindowText;
            this.lstLog.ItemHeight = 12;
            this.lstLog.Location = new System.Drawing.Point(16, 56);
            this.lstLog.Name = "lstLog";
            this.lstLog.RightToLeft = System.Windows.Forms.RightToLeft.No;
            this.lstLog.Size = new System.Drawing.Size(681, 280);
            this.lstLog.TabIndex = 4;
            // 
            // cmdOpen
            // 
            this.cmdOpen.BackColor = System.Drawing.SystemColors.Control;
            this.cmdOpen.Cursor = System.Windows.Forms.Cursors.Default;
            this.cmdOpen.ForeColor = System.Drawing.SystemColors.ControlText;
            this.cmdOpen.Location = new System.Drawing.Point(624, 16);
            this.cmdOpen.Name = "cmdOpen";
            this.cmdOpen.RightToLeft = System.Windows.Forms.RightToLeft.No;
            this.cmdOpen.Size = new System.Drawing.Size(65, 25);
            this.cmdOpen.TabIndex = 3;
            this.cmdOpen.Text = "升级(&U)";
            this.cmdOpen.UseVisualStyleBackColor = false;
            this.cmdOpen.Click += new System.EventHandler(this.cmdOpen_Click);
            // 
            // cmdBrowse
            // 
            this.cmdBrowse.BackColor = System.Drawing.SystemColors.Control;
            this.cmdBrowse.Cursor = System.Windows.Forms.Cursors.Default;
            this.cmdBrowse.ForeColor = System.Drawing.SystemColors.ControlText;
            this.cmdBrowse.Location = new System.Drawing.Point(552, 16);
            this.cmdBrowse.Name = "cmdBrowse";
            this.cmdBrowse.RightToLeft = System.Windows.Forms.RightToLeft.No;
            this.cmdBrowse.Size = new System.Drawing.Size(65, 25);
            this.cmdBrowse.TabIndex = 2;
            this.cmdBrowse.Text = "浏览(&B)";
            this.cmdBrowse.UseVisualStyleBackColor = false;
            this.cmdBrowse.Click += new System.EventHandler(this.cmdBrowse_Click);
            // 
            // txtPath
            // 
            this.txtPath.AcceptsReturn = true;
            this.txtPath.BackColor = System.Drawing.SystemColors.Window;
            this.txtPath.Cursor = System.Windows.Forms.Cursors.IBeam;
            this.txtPath.ForeColor = System.Drawing.SystemColors.WindowText;
            this.txtPath.Location = new System.Drawing.Point(72, 19);
            this.txtPath.MaxLength = 0;
            this.txtPath.Name = "txtPath";
            this.txtPath.RightToLeft = System.Windows.Forms.RightToLeft.No;
            this.txtPath.Size = new System.Drawing.Size(473, 21);
            this.txtPath.TabIndex = 1;
            // 
            // lblNote
            // 
            this.lblNote.BackColor = System.Drawing.Color.Transparent;
            this.lblNote.Cursor = System.Windows.Forms.Cursors.Default;
            this.lblNote.ForeColor = System.Drawing.SystemColors.ControlText;
            this.lblNote.Location = new System.Drawing.Point(16, 344);
            this.lblNote.Name = "lblNote";
            this.lblNote.RightToLeft = System.Windows.Forms.RightToLeft.No;
            this.lblNote.Size = new System.Drawing.Size(681, 105);
            this.lblNote.TabIndex = 5;
            // 
            // lblFolder
            // 
            this.lblFolder.BackColor = System.Drawing.Color.Transparent;
            this.lblFolder.Cursor = System.Windows.Forms.Cursors.Default;
            this.lblFolder.ForeColor = System.Drawing.SystemColors.ControlText;
            this.lblFolder.Location = new System.Drawing.Point(16, 22);
            this.lblFolder.Name = "lblFolder";
            this.lblFolder.RightToLeft = System.Windows.Forms.RightToLeft.No;
            this.lblFolder.Size = new System.Drawing.Size(65, 17);
            this.lblFolder.TabIndex = 0;
            this.lblFolder.Text = "模板路径";
            // 
            // frmMain
            // 
            this.AutoScaleDimensions = new System.Drawing.SizeF(6F, 12F);
            this.AutoScaleMode = System.Windows.Forms.AutoScaleMode.Font;
            this.BackColor = System.Drawing.Color.White;
            this.ClientSize = new System.Drawing.Size(715, 457);
            this.Controls.Add(this.lstLog);
            this.Controls.Add(this.cmdOpen);
            this.Controls.Add(this.cmdBrowse);
            this.Controls.Add(this.txtPath);
            this.Controls.Add(this.lblNote);
            this.Controls.Add(this.lblFolder);
            this.Cursor = System.Windows.Forms.Cursors.Default;
            this.FormBorderStyle = System.Windows.Forms.FormBorderStyle.FixedSingle;
            this.Location = new System.Drawing.Point(514, 330);
            this.MaximizeBox = false;
            this.Name = "frmMain";
            this.RightToLeft = System.Windows.Forms.RightToLeft.No;
            this.StartPosition = System.Windows.Forms.FormStartPosition.Manual;
            this.Text = "ASP Template -> PHP Template";
            this.Load += new System.EventHandler(this.frmMain_Load);
            this.ResumeLayout(false);
            this.PerformLayout();

        }


        #endregion
    }
}

